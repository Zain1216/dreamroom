import * as THREE from 'https://cdn.skypack.dev/three@0.129.0/build/three.module.js';
import { OrbitControls } from 'https://cdn.skypack.dev/three@0.129.0/examples/jsm/controls/OrbitControls.js';

// Outer box group and mesh
let outerBoxGroup;
let outerBoxMesh;

const OUTER_BOX_WIDTH = 5;  // in Three.js units
const OUTER_BOX_HEIGHT = 3;
const OUTER_BOX_DEPTH = 3;

// Add variables for raycasting and dragging furniture
let raycaster = new THREE.Raycaster();
let mouse = new THREE.Vector2();
let selectedFurniture = null;
let offset = new THREE.Vector3();
let plane = new THREE.Plane();
let intersection = new THREE.Vector3();

let scene, camera, renderer, controls;
let room, floor, walls = [];
let furniture = {};
let furnitureGroup;

init();
animate();

function init() {
    // Scene
    scene = new THREE.Scene();

    // Camera
    camera = new THREE.PerspectiveCamera(
        50, // increased FOV from 45 to 50 for wider view
        window.innerWidth / window.innerHeight,
        0.1,
        1000
    );
    camera.position.set(0, 7, 15); // moved camera further back and up for zoom out

    // Renderer
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    const leftPanelWidth = window.innerWidth > 768 ? 320 : 0;
    renderer.setSize(window.innerWidth - leftPanelWidth, window.innerHeight - 120); // responsive width minus header/footer
    document.getElementById('container3D').appendChild(renderer.domElement);

    // Controls
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.screenSpacePanning = false;
    controls.minDistance = 2;
    controls.maxDistance = 50;
    controls.maxPolarAngle = Math.PI / 2;

    // Lights
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(5, 10, 7.5);
    scene.add(directionalLight);

    // Outer box group and mesh
    outerBoxGroup = new THREE.Group();

    const boxGeometry = new THREE.BoxGeometry(OUTER_BOX_WIDTH, OUTER_BOX_HEIGHT, OUTER_BOX_DEPTH);
    const boxEdges = new THREE.EdgesGeometry(boxGeometry);
    const boxMaterial = new THREE.LineBasicMaterial({ color: 0x00ff00 });
    outerBoxMesh = new THREE.LineSegments(boxEdges, boxMaterial);

    // outerBoxGroup.add(outerBoxMesh);
    scene.add(outerBoxGroup);

    // Room setup
    createRoom(50, 50); // default 50x50 feet room (scaled)

    // Furniture group
    furnitureGroup = new THREE.Group();
    outerBoxGroup.add(furnitureGroup);

    // Add default furniture
    // addFurniture('bed');
    // Event listeners for UI
    setupUIListeners();

    window.addEventListener('resize', onWindowResize, false);

    // Add keyboard controls to move outerBoxGroup
    window.addEventListener('keydown', (event) => {
        const step = 0.1;
        switch(event.key) {
            case 'ArrowUp':
                outerBoxGroup.position.z -= step;
                break;
            case 'ArrowDown':
                outerBoxGroup.position.z += step;
                break;
            case 'ArrowLeft':
                outerBoxGroup.position.x -= step;
                break;
            case 'ArrowRight':
                outerBoxGroup.position.x += step;
                break;
            case 'PageUp':
                outerBoxGroup.position.y += step;
                break;
            case 'PageDown':
                outerBoxGroup.position.y -= step;
                break;
        }
    });

    // Add mouse event listeners for furniture dragging
    renderer.domElement.addEventListener('mousedown', onDocumentMouseDown, false);
    renderer.domElement.addEventListener('mousemove', onDocumentMouseMove, false);
    renderer.domElement.addEventListener('mouseup', onDocumentMouseUp, false);

    // Add touch event listeners for furniture dragging on mobile
    renderer.domElement.addEventListener('touchstart', onDocumentTouchStart, false);
    renderer.domElement.addEventListener('touchmove', onDocumentTouchMove, false);
    renderer.domElement.addEventListener('touchend', onDocumentTouchEnd, false);
}

function onDocumentMouseDown(event) {
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();

    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(mouse, camera);

    // Check intersection with furniture meshes and windows/doors
    const furnitureMeshes = [];
    Object.values(furniture).forEach(group => {
        group.children.forEach(child => {
            furnitureMeshes.push(child);
        });
    });

    // Collect windows and doors meshes from all rooms
    const windowDoorMeshes = [];
    [room, secondRoom, thirdRoom, fourthRoom].forEach(roomGroup => {
        if (roomGroup) {
            roomGroup.children.forEach(child => {
                // Assuming windows and doors are Meshes with specific materials or names
                if (child.geometry && (child.material.opacity === 0.6 || child.material.color.equals(new THREE.Color(0x654321)))) {
                    windowDoorMeshes.push(child);
                }
            });
        }
    });

    const allSelectableMeshes = furnitureMeshes.concat(windowDoorMeshes);

    let intersects = raycaster.intersectObjects(allSelectableMeshes, true);

    if (intersects.length > 0) {
        selectedFurniture = intersects[0].object.parent || intersects[0].object; // Select the group containing the mesh or the mesh itself

        // Calculate offset between mouse intersection point and furniture/window/door position
        if (intersects[0].point) {
            plane.setFromNormalAndCoplanarPoint(new THREE.Vector3(0, 1, 0), selectedFurniture.position);
            if (raycaster.ray.intersectPlane(plane, intersection)) {
                offset.copy(intersection).sub(selectedFurniture.position);
            }
        }
    } else {
        // If no furniture or window/door selected, check intersection with room groups for dragging rooms
        const roomGroups = [room, secondRoom, thirdRoom, fourthRoom].filter(r => r !== null && r !== undefined);
        intersects = raycaster.intersectObjects(roomGroups, true);

        if (intersects.length > 0) {
            selectedFurniture = intersects[0].object; // Select the intersected object (room group or child)
            // If intersected object is a child mesh, get its parent group (room)
            while (selectedFurniture.parent && !roomGroups.includes(selectedFurniture)) {
                selectedFurniture = selectedFurniture.parent;
            }

            // Calculate offset between mouse intersection point and room position
            if (intersects[0].point) {
                plane.setFromNormalAndCoplanarPoint(new THREE.Vector3(0, 1, 0), selectedFurniture.position);
                if (raycaster.ray.intersectPlane(plane, intersection)) {
                    offset.copy(intersection).sub(selectedFurniture.position);
                }
            }
        }
    }
}

// Touch start event handler
function onDocumentTouchStart(event) {
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();

    if (event.touches.length === 1) {
        const touch = event.touches[0];
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);

        // Check intersection with furniture meshes
        const furnitureMeshes = [];
        Object.values(furniture).forEach(group => {
            group.children.forEach(child => {
                furnitureMeshes.push(child);
            });
        });

        let intersects = raycaster.intersectObjects(furnitureMeshes, true);

        if (intersects.length > 0) {
            selectedFurniture = intersects[0].object.parent; // Select the group containing the mesh

            // Calculate offset between touch intersection point and furniture position
            if (intersects[0].point) {
                plane.setFromNormalAndCoplanarPoint(new THREE.Vector3(0, 1, 0), selectedFurniture.position);
                if (raycaster.ray.intersectPlane(plane, intersection)) {
                    offset.copy(intersection).sub(selectedFurniture.position);
                }
            }
        } else {
            // If no furniture selected, check intersection with room groups for dragging rooms
            const roomGroups = [room, secondRoom, thirdRoom, fourthRoom].filter(r => r !== null && r !== undefined);
            intersects = raycaster.intersectObjects(roomGroups, true);

            if (intersects.length > 0) {
                selectedFurniture = intersects[0].object; // Select the intersected object (room group or child)
                // If intersected object is a child mesh, get its parent group (room)
                while (selectedFurniture.parent && !roomGroups.includes(selectedFurniture)) {
                    selectedFurniture = selectedFurniture.parent;
                }

                // Calculate offset between touch intersection point and room position
                if (intersects[0].point) {
                    plane.setFromNormalAndCoplanarPoint(new THREE.Vector3(0, 1, 0), selectedFurniture.position);
                    if (raycaster.ray.intersectPlane(plane, intersection)) {
                        offset.copy(intersection).sub(selectedFurniture.position);
                    }
                }
            }
        }
    }
}

// Touch move event handler
function onDocumentTouchMove(event) {
    if (!selectedFurniture) return;
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();

    if (event.touches.length === 1) {
        const touch = event.touches[0];
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);

        if (raycaster.ray.intersectPlane(plane, intersection)) {
            const newPosition = new THREE.Vector3().copy(intersection).sub(offset);

            newPosition.y = selectedFurniture.position.y; // Keep original height

            selectedFurniture.position.copy(newPosition);
        }
    }
}

// Touch end event handler
function onDocumentTouchEnd(event) {
    if (!selectedFurniture) return;
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();
    selectedFurniture = null;
}

// Mouse move event handler
function onDocumentMouseMove(event) {
    if (!selectedFurniture) return;
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();

    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(mouse, camera);

    if (raycaster.ray.intersectPlane(plane, intersection)) {
        const newPosition = new THREE.Vector3().copy(intersection).sub(offset);

        // Constrain newPosition inside the room boundaries (floor size)
        // const floorWidth = floor.geometry.parameters.width / 2;
        // const floorHeight = floor.geometry.parameters.height / 2;

        // newPosition.x = THREE.MathUtils.clamp(newPosition.x, -floorWidth, floorWidth);
        // newPosition.z = THREE.MathUtils.clamp(newPosition.z, -floorHeight, floorHeight);
        newPosition.y = selectedFurniture.position.y; // Keep original height

        selectedFurniture.position.copy(newPosition);
    }
}

// Mouse up event handler
function onDocumentMouseUp(event) {
    if (!selectedFurniture) return;
    if (controls.enabled) return; // Only allow dragging when controls are disabled (locked)

    event.preventDefault();
    selectedFurniture = null;
}

function createWindowMesh(width, height, depth) {
    const geometry = new THREE.BoxGeometry(width, height, depth);
    const material = new THREE.MeshStandardMaterial({
        color: 0x87ceeb,
        transparent: true,
        opacity: 0.6,
        side: THREE.DoubleSide,
    });
    return new THREE.Mesh(geometry, material);
}

function createDoorMesh(width, height, depth) {
    const geometry = new THREE.BoxGeometry(width, height, depth);
    const material = new THREE.MeshStandardMaterial({
        color: 0x654321,
        side: THREE.DoubleSide,
    });
    return new THREE.Mesh(geometry, material);
}

function createRoom(widthFeet, heightFeet) {
    // Clear previous room if any
    if (room) {
        outerBoxGroup.remove(room);
        walls.forEach(wall => outerBoxGroup.remove(wall));
        walls = [];
    }

    const width = widthFeet / 12; // scale down for Three.js units
    const height = heightFeet / 12; // scale down for Three.js units

    // Floor
    const floorGeometry = new THREE.PlaneGeometry(width, height);
    const floorMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room1FloorColor').value });
    floor = new THREE.Mesh(floorGeometry, floorMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.position.y = 0;

    // Walls (4)
    const wallHeight = 1.;
    const wallThickness = 0.1;
    const wallMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room1WallColor').value });

    // Back wall
    const backWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    backWall.position.set(0, wallHeight / 2, -height / 2);

    // Front wall
    const frontWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    frontWall.position.set(0, wallHeight / 2, height / 2);

    // Left wall
    const leftWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    leftWall.position.set(-width / 2, wallHeight / 2, 0);

    // Right wall
    const rightWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    rightWall.position.set(width / 2, wallHeight / 2, 0);

    walls.push(backWall, frontWall, leftWall, rightWall);

    room = new THREE.Group();
    room.add(floor);
    walls.forEach(wall => room.add(wall));

    // Add windows to walls
    const windowWidth = 0.5;
    const windowHeight = 0.5;
    const windowDepth = 0.05;
    const windowMesh = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMesh.position.set(0, wallHeight / 2, -height / 2 + wallThickness / 2 + windowDepth / 2);
    room.add(windowMesh);

    const windowMeshOpposite = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMeshOpposite.position.set(0, wallHeight / 2, -height / 2 - wallThickness / 2 - windowDepth / 2);
    room.add(windowMeshOpposite);

    // Left wall windows
    const leftWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMesh.position.set(-width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    room.add(leftWindowMesh);

    const leftWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMeshOpposite.position.set(-width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    room.add(leftWindowMeshOpposite);

    // Right wall windows
    const rightWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMesh.position.set(width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    room.add(rightWindowMesh);

    const rightWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMeshOpposite.position.set(width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    room.add(rightWindowMeshOpposite);

    // Add door to selected wall based on doorPlacement input
    const doorWidth = 0.7;
    const doorHeight = 0.9;
    const doorDepth = 0.1;
    const doorPlacement = document.getElementById('doorPlacement').value;

    const doorMesh = createDoorMesh(doorWidth, doorHeight, doorDepth);
    const doorMeshOpposite = createDoorMesh(doorWidth, doorHeight, doorDepth);

    switch (doorPlacement) {
        case 'front':
        case 'center':
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
            break;
        case 'back':
        case 'other':
            doorMesh.position.set(0, doorHeight / 2, -height / 2 - wallThickness / 2 - doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, -height / 2 + wallThickness / 2 + doorDepth / 2);
            break;
        case 'left':
            doorMesh.position.set(-width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(-width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        case 'right':
            doorMesh.position.set(width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        default:
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
    }

    room.add(doorMesh);
    room.add(doorMeshOpposite);

    // Add room number label
    const label = createTextLabel('Room 1');
    label.position.set(0, 0.1, 0);
    room.add(label);

    outerBoxGroup.add(room);
}

// Helper function to create text label sprite
function createTextLabel(text) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const fontSize = 64;
    context.font = `${fontSize}px Arial`;
    const textWidth = context.measureText(text).width;
    canvas.width = textWidth;
    canvas.height = fontSize * 1.2;
    context.font = `${fontSize}px Arial`;
    context.fillStyle = 'white';
    context.fillText(text, 0, fontSize);

    const texture = new THREE.CanvasTexture(canvas);
    texture.minFilter = THREE.LinearFilter;
    texture.wrapS = THREE.ClampToEdgeWrapping;
    texture.wrapT = THREE.ClampToEdgeWrapping;

    const spriteMaterial = new THREE.SpriteMaterial({ map: texture, transparent: true });
    const sprite = new THREE.Sprite(spriteMaterial);
    sprite.scale.set(textWidth / 100, fontSize / 100, 1);
    return sprite;
}

// New function to create the third room
let thirdRoom, thirdRoomWalls = [];

function createThirdRoom(widthFeet, heightFeet) {
    // Clear previous third room if any
    if (thirdRoom) {
        outerBoxGroup.remove(thirdRoom);
        thirdRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        thirdRoomWalls = [];
    }

    const width = widthFeet / 12; // scale down for Three.js units
    const height = heightFeet / 12; // scale down for Three.js units

    // Floor
    const floorGeometry = new THREE.PlaneGeometry(width, height);
    const floorMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room3FloorColor').value });
    const floorMesh = new THREE.Mesh(floorGeometry, floorMaterial);
    floorMesh.rotation.x = -Math.PI / 2;
    floorMesh.position.y = 0;

    // Walls (4)
    const wallHeight = 1.;
    const wallThickness = 0.1;
    const wallMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room3WallColor').value });

    // Back wall
    const backWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    backWall.position.set(0, wallHeight / 2, -height / 2);

    // Front wall
    const frontWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    frontWall.position.set(0, wallHeight / 2, height / 2);

    // Left wall
    const leftWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    leftWall.position.set(-width / 2, wallHeight / 2, 0);

    // Right wall
    const rightWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    rightWall.position.set(width / 2, wallHeight / 2, 0);

    thirdRoomWalls.push(backWall, frontWall, leftWall, rightWall);

    thirdRoom = new THREE.Group();
    thirdRoom.add(floorMesh);
    thirdRoomWalls.forEach(wall => thirdRoom.add(wall));

    // Add windows and doors to walls
    // Back wall windows
    const windowWidth = 0.5;
    const windowHeight = 0.5;
    const windowDepth = 0.05;
    const windowMesh = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMesh.position.set(0, wallHeight / 2, -height / 2 + wallThickness / 2 + windowDepth / 2);
    thirdRoom.add(windowMesh);

    const windowMeshOpposite = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMeshOpposite.position.set(0, wallHeight / 2, -height / 2 - wallThickness / 2 - windowDepth / 2);
    thirdRoom.add(windowMeshOpposite);

    // Left wall windows
    const leftWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMesh.position.set(-width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    thirdRoom.add(leftWindowMesh);

    const leftWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMeshOpposite.position.set(-width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    thirdRoom.add(leftWindowMeshOpposite);

    // Right wall windows
    const rightWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMesh.position.set(width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    thirdRoom.add(rightWindowMesh);

    const rightWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMeshOpposite.position.set(width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    thirdRoom.add(rightWindowMeshOpposite);

    // Front wall doors
    const doorWidth = 0.7;
    const doorHeight = 0.9;
    const doorDepth = 0.1;
    const doorPlacement = document.getElementById('doorPlacement3').value;

    const doorMesh = createDoorMesh(doorWidth, doorHeight, doorDepth);
    const doorMeshOpposite = createDoorMesh(doorWidth, doorHeight, doorDepth);

    switch (doorPlacement) {
        case 'front':
        case 'center':
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
            break;
        case 'back':
        case 'other':
            doorMesh.position.set(0, doorHeight / 2, -height / 2 - wallThickness / 2 - doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, -height / 2 + wallThickness / 2 + doorDepth / 2);
            break;
        case 'left':
            doorMesh.position.set(-width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(-width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        case 'right':
            doorMesh.position.set(width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        default:
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
    }

    thirdRoom.add(doorMesh);
    thirdRoom.add(doorMeshOpposite);

  

    // Add room number label
    const label = createTextLabel('Room 3');
    label.position.set(0, 0.1, 0);
    thirdRoom.add(label);

    // Position the third room next to the second room on the X axis
    const firstRoomWidth = parseInt(document.getElementById('roomWidth').value) / 12;
    const secondRoomWidth = parseInt(document.getElementById('room2Width').value) / 12;
    thirdRoom.position.set(firstRoomWidth + secondRoomWidth + 0.4, 0, 0);

    outerBoxGroup.add(thirdRoom);
}

// New variables for fourth room
let fourthRoom, fourthRoomWalls = [];

// New function to create the fourth room
function createFourthRoom(widthFeet, heightFeet) {
    // Clear previous fourth room if any
    if (fourthRoom) {
        outerBoxGroup.remove(fourthRoom);
        fourthRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        fourthRoomWalls = [];
    }

    const width = widthFeet / 12; // scale down for Three.js units
    const height = heightFeet / 12; // scale down for Three.js units

    // Floor
    const floorGeometry = new THREE.PlaneGeometry(width, height);
    const floorMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room4FloorColor').value });
    const floorMesh = new THREE.Mesh(floorGeometry, floorMaterial);
    floorMesh.rotation.x = -Math.PI / 2;
    floorMesh.position.y = 0;

    // Walls (4)
    const wallHeight = 1.;
    const wallThickness = 0.1;
    const wallMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room4WallColor').value });

    // Back wall
    const backWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    backWall.position.set(0, wallHeight / 2, -height / 2);

    // Front wall
    const frontWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    frontWall.position.set(0, wallHeight / 2, height / 2);

    // Left wall
    const leftWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    leftWall.position.set(-width / 2, wallHeight / 2, 0);

    // Right wall
    const rightWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    rightWall.position.set(width / 2, wallHeight / 2, 0);

    fourthRoomWalls.push(backWall, frontWall, leftWall, rightWall);

    fourthRoom = new THREE.Group();
    fourthRoom.add(floorMesh);
    fourthRoomWalls.forEach(wall => fourthRoom.add(wall));

    // Add windows and doors to walls
    // Back wall windows
    const windowWidth = 0.5;
    const windowHeight = 0.5;
    const windowDepth = 0.05;
    const windowMesh = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMesh.position.set(0, wallHeight / 2, -height / 2 + wallThickness / 2 + windowDepth / 2);
    fourthRoom.add(windowMesh);

    const windowMeshOpposite = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMeshOpposite.position.set(0, wallHeight / 2, -height / 2 - wallThickness / 2 - windowDepth / 2);
    fourthRoom.add(windowMeshOpposite);
    

    // Left wall windows
    const leftWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMesh.position.set(-width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    fourthRoom.add(leftWindowMesh);

    const leftWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMeshOpposite.position.set(-width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    fourthRoom.add(leftWindowMeshOpposite);

    // Right wall windows
    const rightWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMesh.position.set(width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    fourthRoom.add(rightWindowMesh);

    const rightWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMeshOpposite.position.set(width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    fourthRoom.add(rightWindowMeshOpposite);

    


    // Front wall doors
    const doorWidth = 0.7;
    const doorHeight = 0.9;
    const doorDepth = 0.1;
    const doorPlacement = document.getElementById('doorPlacement4').value;

    const doorMesh = createDoorMesh(doorWidth, doorHeight, doorDepth);
    const doorMeshOpposite = createDoorMesh(doorWidth, doorHeight, doorDepth);

    switch (doorPlacement) {
        case 'front':
        case 'center':
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
            break;
        case 'back':
        case 'other':
            doorMesh.position.set(0, doorHeight / 2, -height / 2 - wallThickness / 2 - doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, -height / 2 + wallThickness / 2 + doorDepth / 2);
            break;
        case 'left':
            doorMesh.position.set(-width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(-width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        case 'right':
            doorMesh.position.set(width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        default:
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
    }

    fourthRoom.add(doorMesh);
    fourthRoom.add(doorMeshOpposite);

 

    // Add room number label
    const label = createTextLabel('Room 4');
    label.position.set(0, 0.1, 0);
    fourthRoom.add(label);

    // Position the fourth room next to the third room on the X axis
    const firstRoomWidth = parseInt(document.getElementById('roomWidth').value) / 12;
    const secondRoomWidth = parseInt(document.getElementById('room2Width').value) / 12;
    const thirdRoomWidth = parseInt(document.getElementById('room3Width').value) / 12;
    fourthRoom.position.set(firstRoomWidth + secondRoomWidth + thirdRoomWidth + 0.6, 0, 0);

    outerBoxGroup.add(fourthRoom);
}

function updateFurnitureVisibility() {
    const checkboxes = document.querySelectorAll('input[name="furniture"]');
    checkboxes.forEach(cb => {
        if (cb.checked) {
            const quantityInput = document.getElementById(cb.value + 'Quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            addFurniture(cb.value, quantity);
        } else {
            // Remove all furniture groups whose keys start with cb.value
            Object.keys(furniture).forEach(key => {
                if (key.startsWith(cb.value)) {
                    furnitureGroup.remove(furniture[key]);
                    delete furniture[key];
                }
            });
        }
    });
}

function addFurniture(type, quantity = 1) {
    // Remove existing furniture of this type if any
    Object.keys(furniture).forEach(key => {
        if (key.startsWith(type)) {
            furnitureGroup.remove(furniture[key]);
            delete furniture[key];
        }
    });

    const bedroomCategory = document.getElementById('bedroomCategory').value;

    // Get bed customization inputs
    const bedWidthInput = parseFloat(document.getElementById('bedWidth').value) || 1.2;
    const bedHeightInput = parseFloat(document.getElementById('bedHeight').value) || 1.5;
    const bedColorInput = document.getElementById('bedColor').value || '#8B4513';

    // Helper functions to create meshes
    function createBedMesh() {
        const bedWidth = bedWidthInput;
        const bedDepth = bedHeightInput;
        const bedHeight = 0.5; // fixed vertical height
        const geometry = new THREE.BoxGeometry(bedWidth, bedHeight, bedDepth);
        const material = new THREE.MeshStandardMaterial({ color: new THREE.Color(bedColorInput) });
        return new THREE.Mesh(geometry, material);
    }

    function createTableMesh() {
        const tableWidth = 1;
        const tableHeight = 0.75;
        const tableDepth = 1;
        const geometry = new THREE.BoxGeometry(tableWidth, tableHeight, tableDepth);
        const material = new THREE.MeshStandardMaterial({ color: 0x654321 }); // dark brown
        return new THREE.Mesh(geometry, material);
    }

    function createLightMesh() {
        const sphereGeometry = new THREE.SphereGeometry(0.1, 16, 16);
        const sphereMaterial = new THREE.MeshBasicMaterial({ color: 0xffffaa });
        return new THREE.Mesh(sphereGeometry, sphereMaterial);
    }

    function createPointLight() {
        return new THREE.PointLight(0xffffaa, 1, 5);
    }

    for (let i = 1; i <= quantity; i++) {
        const furnitureGroupLocal = new THREE.Group();

        if (type === 'bed') {
            if (!room) continue;
            const bedMesh = createBedMesh();
            bedMesh.position.set(0, 0.5 / 2, 0);
            furnitureGroupLocal.add(bedMesh);

            // Position beds in rooms based on bedroomCategory and index
            if (i === 1) {
                furnitureGroupLocal.position.set(room.position.x, 0, 0);
                furniture['bed1'] = furnitureGroupLocal;
            } else if (i === 2 && (bedroomCategory === '2bed' || bedroomCategory === '3bed' || bedroomCategory === '4bed') && secondRoom) {
                furnitureGroupLocal.position.set(secondRoom.position.x, 0, 0);
                furniture['bed2'] = furnitureGroupLocal;
            } else if (i === 3 && (bedroomCategory === '3bed' || bedroomCategory === '4bed') && thirdRoom) {
                furnitureGroupLocal.position.set(thirdRoom.position.x, 0, 0);
                furniture['bed3'] = furnitureGroupLocal;
            } else if (i === 4 && bedroomCategory === '4bed' && fourthRoom) {
                furnitureGroupLocal.position.set(fourthRoom.position.x, 0, 0);
                furniture['bed4'] = furnitureGroupLocal;
            } else {
                // For quantities beyond 4, position beds in a row in the first room
                furnitureGroupLocal.position.set(room.position.x + (i - 1) * 2, 0, 0);
                furniture[`bed_extra_${i}`] = furnitureGroupLocal;
            }
        } else if (type === 'table') {
            if (!room) continue;
            const tableMesh = createTableMesh();
            tableMesh.position.set(0, 0.75 / 2, 1);
            furnitureGroupLocal.add(tableMesh);

            if (i === 1) {
                furnitureGroupLocal.position.set(room.position.x, 0, 0);
                furniture['table1'] = furnitureGroupLocal;
            } else if (i === 2 && (bedroomCategory === '2bed' || bedroomCategory === '3bed' || bedroomCategory === '4bed') && secondRoom) {
                furnitureGroupLocal.position.set(secondRoom.position.x, 0, 0);
                furniture['table2'] = furnitureGroupLocal;
            } else if (i === 3 && (bedroomCategory === '3bed' || bedroomCategory === '4bed') && thirdRoom) {
                furnitureGroupLocal.position.set(thirdRoom.position.x, 0, 0);
                furniture['table3'] = furnitureGroupLocal;
            } else if (i === 4 && bedroomCategory === '4bed' && fourthRoom) {
                furnitureGroupLocal.position.set(fourthRoom.position.x, 0, 0);
                furniture['table4'] = furnitureGroupLocal;
            } else {
                // For quantities beyond 4, position tables in a row in the first room
                furnitureGroupLocal.position.set(room.position.x + (i - 1) * 1.5, 0, 1);
                furniture[`table_extra_${i}`] = furnitureGroupLocal;
            }
        } else if (type === 'lights') {
            if (!room) continue;
            const lightMesh = createLightMesh();
            const pointLight = createPointLight();
            lightMesh.position.set(0, 2.5, 0);
            pointLight.position.set(0, 2.5, 0);
            furnitureGroupLocal.add(lightMesh);
            furnitureGroupLocal.add(pointLight);

            if (i === 1) {
                furnitureGroupLocal.position.set(room.position.x, 0, 0);
                furniture['lights1'] = furnitureGroupLocal;
            } else if (i === 2 && (bedroomCategory === '2bed' || bedroomCategory === '3bed' || bedroomCategory === '4bed') && secondRoom) {
                furnitureGroupLocal.position.set(secondRoom.position.x, 0, 0);
                furniture['lights2'] = furnitureGroupLocal;
            } else if (i === 3 && (bedroomCategory === '3bed' || bedroomCategory === '4bed') && thirdRoom) {
                furnitureGroupLocal.position.set(thirdRoom.position.x, 0, 0);
                furniture['lights3'] = furnitureGroupLocal;
            } else if (i === 4 && bedroomCategory === '4bed' && fourthRoom) {
                furnitureGroupLocal.position.set(fourthRoom.position.x, 0, 0);
                furniture['lights4'] = furnitureGroupLocal;
            } else {
                // For quantities beyond 4, position lights in a row in the first room
                furnitureGroupLocal.position.set(room.position.x + (i - 1) * 1.5, 0, 0);
                furniture[`lights_extra_${i}`] = furnitureGroupLocal;
            }
        }

        furnitureGroup.add(furnitureGroupLocal);
    }
}


function updateColors() {
    // Update colors for room 1
    if (room && walls.length > 0 && floor) {
        const wallColor1 = document.getElementById('room1WallColor').value;
        const floorColor1 = document.getElementById('room1FloorColor').value;
        walls.forEach(wall => {
            wall.material.color.set(wallColor1);
        });
        floor.material.color.set(floorColor1);
    }

    // Update colors for room 2
    if (secondRoom && secondRoomWalls.length > 0) {
        const wallColor2 = document.getElementById('room2WallColor').value;
        const floorColor2 = document.getElementById('room2FloorColor').value;
        secondRoomWalls.forEach(wall => {
            wall.material.color.set(wallColor2);
        });
        // floor mesh is first child of secondRoom group
        if (secondRoom.children.length > 0) {
            secondRoom.children[0].material.color.set(floorColor2);
        }
    }

    // Update colors for room 3
    if (thirdRoom && thirdRoomWalls.length > 0) {
        const wallColor3 = document.getElementById('room3WallColor').value;
        const floorColor3 = document.getElementById('room3FloorColor').value;
        thirdRoomWalls.forEach(wall => {
            wall.material.color.set(wallColor3);
        });
        if (thirdRoom.children.length > 0) {
            thirdRoom.children[0].material.color.set(floorColor3);
        }
    }

    // Update colors for room 4
    if (fourthRoom && fourthRoomWalls.length > 0) {
        const wallColor4 = document.getElementById('room4WallColor').value;
        const floorColor4 = document.getElementById('room4FloorColor').value;
        fourthRoomWalls.forEach(wall => {
            wall.material.color.set(wallColor4);
        });
        if (fourthRoom.children.length > 0) {
            fourthRoom.children[0].material.color.set(floorColor4);
        }
    }

    // Update scene background color from backgroundColor input
    const bgColor = document.getElementById('backgroundColor').value;
    scene.background = new THREE.Color(bgColor);
}

function updateRoomSize() {
    const width1 = parseInt(document.getElementById('roomWidth').value);
    const height1 = parseInt(document.getElementById('roomHeight').value);
    const width2 = parseInt(document.getElementById('room2Width').value);
    const height2 = parseInt(document.getElementById('room2Height').value);
    const width3 = parseInt(document.getElementById('room3Width').value);
    const height3 = parseInt(document.getElementById('room3Height').value);
    const width4 = parseInt(document.getElementById('room4Width').value);
    const height4 = parseInt(document.getElementById('room4Height').value);
    const bedroomCategory = document.getElementById('bedroomCategory').value;

    // Store current positions to preserve user moved positions
    const roomPositions = {
        room: room ? room.position.clone() : null,
        secondRoom: secondRoom ? secondRoom.position.clone() : null,
        thirdRoom: thirdRoom ? thirdRoom.position.clone() : null,
        fourthRoom: fourthRoom ? fourthRoom.position.clone() : null,
    };

    // Calculate total width for centering with extra spacing between rooms
    let totalWidth = 0;
    const spacing = 2.5; // increased spacing between rooms for better separation
    if (width1) totalWidth += width1 / 6
    if (width2) totalWidth += width2 / 50;
    // if ((bedroomCategory === '2bed' || bedroomCategory == '3bed' || bedroomCategory === '4bed') && width2) totalWidth += width2 / 12;
    // if ((bedroomCategory === '3bed' || bedroomCategory == '4bed') && width3) totalWidth += width3 / 12;
    // if (bedroomCategory === '4bed' && width4) totalWidth += width4 / 2;

    // Starting X position to center rooms
    let startX = -totalWidth / 2;

    if (
        width1 && height1
    ) {
        createRoom(width1, height1);
        if (roomPositions.room) {
            room.position.copy(roomPositions.room);
        } else {
            room.position.set(startX + (width1 / 12) / 2, 0, 0);
        }
        startX += width1 / 12 + spacing;
    }

    if ((bedroomCategory === '2bed' || bedroomCategory === '3bed' || bedroomCategory === '4bed') &&
        width2 && height2
    ) {
        createSecondRoom(width2, height2);
        if (roomPositions.secondRoom) {
            secondRoom.position.copy(roomPositions.secondRoom);
        } else {
            secondRoom.position.set(startX + (width2 / 12) / 2, 0, 0);
        }
        startX += width2 / 12;
    }

    if ((bedroomCategory === '3bed' || bedroomCategory === '4bed') &&
        width3 && height3
    ) {
        createThirdRoom(width3, height3);
        if (roomPositions.thirdRoom) {
            thirdRoom.position.copy(roomPositions.thirdRoom);
        } else {
            thirdRoom.position.set(startX + (width3 / 12) / 2, 0, 0);
        }
        startX += width3 / 12;
    }

    if (bedroomCategory === '4bed' &&
        width4 && height4
    ) {
        createFourthRoom(width4, height4);
        if (roomPositions.fourthRoom) {
            fourthRoom.position.copy(roomPositions.fourthRoom);
        } else {
            fourthRoom.position.set(startX + (width4 / 12) / 2, 0, 0);
        }
            startX += width3 / 9 ;
;
    }

    updateColors();
    updateFurnitureVisibility();

    // Add event listeners for door placement inputs for rooms 2, 3, and 4 to update room size on change
    // Removed doorPlacement for room 1 to delink it
    const doorPlacement2 = document.getElementById('doorPlacement2');
    if (doorPlacement2) {
        doorPlacement2.addEventListener('change', () => {
            updateRoomSize();
        });
    }
    const doorPlacement3 = document.getElementById('doorPlacement3');
    if (doorPlacement3) {
        doorPlacement3.addEventListener('change', () => {
            updateRoomSize();
        });
    }
    const doorPlacement4 = document.getElementById('doorPlacement4');
    if (doorPlacement4) {
        doorPlacement4.addEventListener('change', () => {
            updateRoomSize();
        });
    }
}

// New function to create the second room
let secondRoom, secondRoomWalls = [];

function createSecondRoom(widthFeet, heightFeet) {
    // Clear previous second room if any
    if (secondRoom) {
        outerBoxGroup.remove(secondRoom);
        secondRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        secondRoomWalls = [];
    }

    const width = widthFeet / 12; // scale down for Three.js units
    const height = heightFeet / 12; // scale down for Three.js units

    // Floor
    const floorGeometry = new THREE.PlaneGeometry(width, height);
    const floorMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room2FloorColor').value });
    const floorMesh = new THREE.Mesh(floorGeometry, floorMaterial);
    floorMesh.rotation.x = -Math.PI / 2;
    floorMesh.position.y = 0;

    // Walls (4)
    const wallHeight = 1.;
    const wallThickness = 0.1;
    const wallMaterial = new THREE.MeshStandardMaterial({ color: document.getElementById('room2WallColor').value });

    // Back wall
    const backWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    backWall.position.set(0, wallHeight / 2, -height / 2);

    // Front wall
    const frontWall = new THREE.Mesh(new THREE.BoxGeometry(width, wallHeight, wallThickness), wallMaterial);
    frontWall.position.set(0, wallHeight / 2, height / 2);

    // Left wall
    const leftWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    leftWall.position.set(-width / 2, wallHeight / 2, 0);

    // Right wall
    const rightWall = new THREE.Mesh(new THREE.BoxGeometry(wallThickness, wallHeight, height), wallMaterial);
    rightWall.position.set(width / 2, wallHeight / 2, 0);

    secondRoomWalls.push(backWall, frontWall, leftWall, rightWall);

    secondRoom = new THREE.Group();
    secondRoom.add(floorMesh);
    secondRoomWalls.forEach(wall => secondRoom.add(wall));

    // Add windows and doors to walls
    // Back wall windows
    const windowWidth = 0.5;
    const windowHeight = 0.5;
    const windowDepth = 0.05;
    const windowMesh = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMesh.position.set(0, wallHeight / 2, -height / 2 + wallThickness / 2 + windowDepth / 2);
    secondRoom.add(windowMesh);

    const windowMeshOpposite = createWindowMesh(windowWidth, windowHeight, windowDepth);
    windowMeshOpposite.position.set(0, wallHeight / 2, -height / 2 - wallThickness / 2 - windowDepth / 2);
    secondRoom.add(windowMeshOpposite);

    // Left wall windows
    const leftWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMesh.position.set(-width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    secondRoom.add(leftWindowMesh);

    const leftWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    leftWindowMeshOpposite.position.set(-width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    secondRoom.add(leftWindowMeshOpposite);

    // Right wall windows
    const rightWindowMesh = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMesh.position.set(width / 2 + wallThickness / 2 + windowDepth / 2, wallHeight / 2, 0);
    secondRoom.add(rightWindowMesh);

    const rightWindowMeshOpposite = createWindowMesh(windowDepth, windowHeight, windowWidth);
    rightWindowMeshOpposite.position.set(width / 2 - wallThickness / 2 - windowDepth / 2, wallHeight / 2, 0);
    secondRoom.add(rightWindowMeshOpposite);

    // Front wall doors
    const doorWidth = 0.7;
    const doorHeight = 0.9;
    const doorDepth = 0.1;
    const doorPlacement = document.getElementById('doorPlacement2').value;

    const doorMesh = createDoorMesh(doorWidth, doorHeight, doorDepth);
    const doorMeshOpposite = createDoorMesh(doorWidth, doorHeight, doorDepth);

    switch (doorPlacement) {
        case 'front':
        case 'center':
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
            break;
        case 'back':
        case 'other':
            doorMesh.position.set(0, doorHeight / 2, -height / 2 - wallThickness / 2 - doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, -height / 2 + wallThickness / 2 + doorDepth / 2);
            break;
        case 'left':
            doorMesh.position.set(-width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(-width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        case 'right':
            doorMesh.position.set(width / 2 + wallThickness / 2 + doorDepth / 2, doorHeight / 2, 0);
            doorMesh.rotation.y = Math.PI / 2;
            doorMeshOpposite.position.set(width / 2 - wallThickness / 2 - doorDepth / 2, doorHeight / 2, 0);
            doorMeshOpposite.rotation.y = Math.PI / 2;
            break;
        default:
            doorMesh.position.set(0, doorHeight / 2, height / 2 + wallThickness / 2 + doorDepth / 2);
            doorMeshOpposite.position.set(0, doorHeight / 2, height / 2 - wallThickness / 2 - doorDepth / 2);
    }

    secondRoom.add(doorMesh);
    secondRoom.add(doorMeshOpposite);


    // Add room number label for Room 2
    const label = createTextLabel('Room 2');
    label.position.set(0, 0.1, 0);
    secondRoom.add(label);

    // Position the second room next to the first room on the X axis with increased spacing for separation
    const firstRoomWidth = parseInt(document.getElementById('roomWidth').value) / 12;
    const separationSpacing = 1.0; // increased spacing for clear separation
    secondRoom.position.set(firstRoomWidth + separationSpacing, 0, 0);

    outerBoxGroup.add(secondRoom);
}

function setupUIListeners() {
    // Remove listeners for old single wallColor and floorColor inputs
    // Add listeners for new room 1 wall and floor color inputs
    document.getElementById('room1WallColor').addEventListener('input', () => {
        updateColors();
    });
    document.getElementById('room1FloorColor').addEventListener('input', () => {
        updateColors();
    });

    // Add listener for door placement select input to update room on change
    document.getElementById('doorPlacement').addEventListener('change', () => {
        updateRoomSize();
    });

    // Add listeners for bed customization inputs to update beds dynamically
    document.getElementById('bedWidth').addEventListener('input', () => {
        updateBedDimensionsAndColor();
    });
    document.getElementById('bedHeight').addEventListener('input', () => {
        updateBedDimensionsAndColor();
    });
    document.getElementById('bedColor').addEventListener('input', () => {
        updateBedDimensionsAndColor();
    });

    // Add listeners for background color and canvas background color inputs
    document.getElementById('backgroundColor').addEventListener('input', () => {
        updateColors();
    });

    document.getElementById('roomWidth').addEventListener('input', () => {
        updateRoomSize();
    });
    document.getElementById('roomHeight').addEventListener('input', () => {
        updateRoomSize();
    });

    // Add event listeners for furniture quantity inputs to update furniture visibility
    const bedQuantityInput = document.getElementById('bedQuantity');
    if (bedQuantityInput) {
        bedQuantityInput.addEventListener('input', () => {
            updateFurnitureVisibility();
        });
    }
    const tableQuantityInput = document.getElementById('tableQuantity');
    if (tableQuantityInput) {
        tableQuantityInput.addEventListener('input', () => {
            updateFurnitureVisibility();
        });
    }
    const lightsQuantityInput = document.getElementById('lightsQuantity');
    if (lightsQuantityInput) {
        lightsQuantityInput.addEventListener('input', () => {
            updateFurnitureVisibility();
        });
    }

    // Show/hide second, third, and fourth room inputs based on bedroom category
document.getElementById('bedroomCategory').addEventListener('change', (event) => {
    // Save furniture checkbox states
    const furnitureCheckboxes = document.querySelectorAll('input[name="furniture"]');
    const furnitureStates = {};
    furnitureCheckboxes.forEach(cb => {
        furnitureStates[cb.value] = cb.checked;
    });

    const value = event.target.value;

    const secondRoomInputs = document.getElementById('secondRoomInputs');
    const thirdRoomInputs = document.getElementById('thirdRoomInputs');
    const fourthRoomInputs = document.getElementById('fourthRoomInputs');

    // Show/hide UI sections
    secondRoomInputs.style.display = (value === '2bed' || value === '3bed' || value === '4bed') ? 'block' : 'none';
    thirdRoomInputs.style.display  = (value === '3bed' || value === '4bed') ? 'block' : 'none';
    fourthRoomInputs.style.display = (value === '4bed') ? 'block' : 'none';

    // Remove old rooms
    if (secondRoom) {
        outerBoxGroup.remove(secondRoom);
        secondRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        secondRoomWalls = [];
        secondRoom = null;
    }
    if (thirdRoom) {
        outerBoxGroup.remove(thirdRoom);
        thirdRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        thirdRoomWalls = [];
        thirdRoom = null;
    }
    if (fourthRoom) {
        outerBoxGroup.remove(fourthRoom);
        fourthRoomWalls.forEach(wall => outerBoxGroup.remove(wall));
        fourthRoomWalls = [];
        fourthRoom = null;
    }

    // ✅ Remove all furniture from the group
    furnitureGroup.clear(); 
    // furniture = {}; 

    updateRoomSize();           // recreate rooms
    updateFurnitureVisibility(); // re-add furniture based on current checkbox + new room count

    // Restore furniture checkbox states
    furnitureCheckboxes.forEach(cb => {
        if (furnitureStates.hasOwnProperty(cb.value)) {
            cb.checked = furnitureStates[cb.value];
        }
    });
});



    // Add listeners for second room inputs
    document.getElementById('room2Width').addEventListener('input', () => {
        updateRoomSize();
    });
    document.getElementById('room2Height').addEventListener('input', () => {
        updateRoomSize();
    });
    // Add listeners for second room wall and floor color inputs
    document.getElementById('room2WallColor').addEventListener('input', () => {
        updateColors();
    });
    document.getElementById('room2FloorColor').addEventListener('input', () => {
        updateColors();
    });

    // Add listeners for third room inputs
    document.getElementById('room3Width').addEventListener('input', () => {
        updateRoomSize();
    });
    document.getElementById('room3Height').addEventListener('input', () => {
        updateRoomSize();
    });
    // Add listeners for third room wall and floor color inputs
    document.getElementById('room3WallColor').addEventListener('input', () => {
        updateColors();
    });
    document.getElementById('room3FloorColor').addEventListener('input', () => {
        updateColors();
    });

    // Add listeners for fourth room inputs
    document.getElementById('room4Width').addEventListener('input', () => {
        updateRoomSize();
    });
    document.getElementById('room4Height').addEventListener('input', () => {
        updateRoomSize();
    });
    // Add listeners for fourth room wall and floor color inputs
    document.getElementById('room4WallColor').addEventListener('input', () => {
        updateColors();
    });
    document.getElementById('room4FloorColor').addEventListener('input', () => {
        updateColors();
    });

    const furnitureCheckboxes = document.querySelectorAll('input[name="furniture"]');
    furnitureCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            updateFurnitureVisibility();
        });
    });
    document.getElementById('resetBtn').addEventListener('click', () => {
        location.reload();
    });

    // Add listener for 3D motion lock/unlock button
    const toggleMotionBtn = document.getElementById('toggleMotionBtn');
    toggleMotionBtn.addEventListener('click', () => {
        controls.enabled = !controls.enabled;
        if (controls.enabled) {
            toggleMotionBtn.textContent = 'Lock 3D Motion';
            toggleMotionBtn.classList.remove('locked');
        } else {
            toggleMotionBtn.textContent = 'Unlock 3D Motion';
            toggleMotionBtn.classList.add('locked');
        }
    });

    // Function to update bed dimensions and color dynamically
    function updateBedDimensionsAndColor() {
        const bedWidthInput = parseFloat(document.getElementById('bedWidth').value) || 1.5;
        const bedHeightInput = parseFloat(document.getElementById('bedHeight').value) || 0.5;
        const bedColorInput = document.getElementById('bedColor').value || '#8B4513';

        const fixedHeight = 0.5; // fixed vertical height

        Object.keys(furniture).forEach(key => {
            if (key.startsWith('bed')) {
                const bedGroup = furniture[key];
                if (!bedGroup) return;

                // Assume the bed mesh is the first child of the group
                const bedMesh = bedGroup.children[0];
                if (!bedMesh) return;

                // Update geometry
                bedMesh.geometry.dispose();
                bedMesh.geometry = new THREE.BoxGeometry(bedWidthInput, fixedHeight, bedHeightInput);

                // Update material color
                bedMesh.material.color.set(bedColorInput);

                // Update bed mesh position to keep it on the floor
                bedMesh.position.set(0, fixedHeight / 2, 0);
            }
        });
    }

    // Add listeners for room name inputs to update labels dynamically
    function updateRoomLabel(roomGroup, newName) {
        if (!roomGroup) return;
        // Find existing label sprite (assumed to be last child)
        const label = roomGroup.children.find(child => child.type === 'Sprite');
        if (label) {
            roomGroup.remove(label);
        }
        const newLabel = createTextLabel(newName || 'Room');
        newLabel.position.set(0, 0.1, 0);
        roomGroup.add(newLabel);
    }

    const room1NameInput = document.getElementById('room1Name');
    if (room1NameInput) {
        room1NameInput.addEventListener('input', (e) => {
            updateRoomLabel(room, e.target.value);
        });
    }

    const room2NameInput = document.getElementById('room2Name');
    if (room2NameInput) {
        room2NameInput.addEventListener('input', (e) => {
            updateRoomLabel(secondRoom, e.target.value);
        });
    }

    const room3NameInput = document.getElementById('room3Name');
    if (room3NameInput) {
        room3NameInput.addEventListener('input', (e) => {
            updateRoomLabel(thirdRoom, e.target.value);
        });
    }

    const room4NameInput = document.getElementById('room4Name');
    if (room4NameInput) {
        room4NameInput.addEventListener('input', (e) => {
            updateRoomLabel(fourthRoom, e.target.value);
        });
    }
}

function onWindowResize() {
    const leftPanelWidth = window.innerWidth > 768 ? 320 : 0;
    const width = window.innerWidth - leftPanelWidth;
    const height = window.innerHeight - 120;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
}

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
}

function showNotification(message) {
    const notification = document.getElementById('notification');
    if (!notification) return;
    notification.textContent = message;
    notification.style.display = 'block';
    clearTimeout(showNotification.timeoutId);
    showNotification.timeoutId = setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// Export the current renderer canvas as an image and trigger download
// Accepts optional parameters: format ('png' or 'jpeg'), scale (number)
function exportAsImage(format = 'png', scale = 1) {
    try {
        renderer.render(scene, camera); // render latest frame

        const originalCanvas = renderer.domElement;

        // Create a temp canvas
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        tempCanvas.width = originalCanvas.width;
        tempCanvas.height = originalCanvas.height;

        // ✅ Add white background for JPEG
        if (format === 'jpeg') {
            tempCtx.fillStyle = '#ffffff';
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        }

        // Draw the original canvas content
        tempCtx.drawImage(originalCanvas, 0, 0);

        const mimeType = format === 'jpeg' ? 'image/jpeg' : 'image/png';
        const dataURL = tempCanvas.toDataURL(mimeType);

        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const filename = `dreamroom3d_${timestamp}.${format === 'jpeg' ? 'jpg' : 'png'}`;

        const link = document.createElement('a');
        link.href = dataURL;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showNotification('Image exported successfully!');
    } catch (error) {
        showNotification('Failed to export image: ' + error.message);
    }
}



// Add event listener to export button
document.getElementById('exportBtn').addEventListener('click', () => {
    const format = document.getElementById('imageFormatSelector').value;
    exportAsImage(format); // pass selected format
});
