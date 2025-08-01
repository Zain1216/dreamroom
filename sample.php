<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Manrope%3Awght%40400%3B500%3B700%3B800&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
  <link rel="stylesheet" href="style.css">
  <title>casahue</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<script src="https://cdn.jsdelivr.net/npm/three@0.154.0/build/three.min.js"></script>
<script defer src="main.js"></script>
<script type="module" src="app.js"></script>

<body>

  <div style='font-family: Manrope, "Noto Sans", sans-serif;'>
    <header class="flex flex-col sm:flex-row items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f1f2f4] px-4 sm:px-10 py-3">
      <div class="flex items-center gap-4 text-[#121517]">
        <a href="/index.html" class="flex items-center space-x-2">
          <div class="size-4 text-black">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor"></path>
            </svg>
          </div>
          <h2 class="text-[#121517] text-lg font-bold leading-tight tracking-[-0.015em]">
            Casahue
          </h2>
        </a>

      </div>
      <div class="flex gap-2 mt-2 sm:mt-0">
        <button
          class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#4e92ca] text-white text-sm font-bold leading-normal tracking-[0.015em]">
          <!-- This is a logout link that only logs out if confirmed -->
          <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');" class=" font-medium truncate">
            Logout
          </a>

        </button>
        <button
          class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f1f2f4] text-[#121517] text-sm font-bold leading-normal tracking-[0.015em]">
          <a class="truncate" href="delete-account.php" onclick="return confirm('Are you sure you want to delete account?');">Delete account</a>
        </button>
      </div>
    </header>

    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: Manrope, "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">

        <div class="container flex flex-col sm:flex-row flex-1 gap-1 px-4 sm:px-6 py-5">
          <aside class="left-panel flex flex-col w-full sm:w-[300px] max-w-full sm:max-w-[300px] shrink-0 gap-4 border-r border-solid border-r-[#f1f2f4] px-4 sm:px-6 py-5">
            <h2>Bedroom Category</h2>
            <select id="bedroomCategory" name="bedroomCategory">
              <option value="1bed">1 Bed</option>
              <option value="2bed">2 Bed</option>
              <option value="3bed">3 Bed</option>
              <option value="4bed">4 Bed</option>
            </select>

            <h2>3D Motion Control</h2>
            <button type="button" id="toggleMotionBtn">Lock 3D Motion</button>

            <h2>Room 1 Name</h2>
            <input type="text" id="room1Name" name="room1Name" value="Room 1" />

            <h2>Room 1 Width (ft.)</h2>
            <input type="number" id="roomWidth" name="roomWidth" min="5" value="50" />

            <h2>Room 1 Height (ft.)</h2>
            <input type="number" id="roomHeight" name="roomHeight" min="5" value="50" />

            <h2>Room 1 Wall Color</h2>
            <input type="color" id="room1WallColor" name="room1WallColor" value="#222222" />

            <h2>Room 1 Floor Color</h2>
            <input type="color" id="room1FloorColor" name="room1FloorColor" value="#474747" />

            <h2>Door Placement on wall for room 1</h2>
            <select id="doorPlacement" name="doorPlacement">
              <option value="center">Front</option>
              <option value="left">Left</option>
              <option value="right">Right</option>
              <option value="other">Back</option>
            </select>

            <div id="secondRoomInputs" style="display:none;">
              <h2>Room 2 Name</h2>
              <input type="text" id="room2Name" name="room2Name" value="Room 2" />

              <h2>Room 2 Width (ft.)</h2>
              <input type="number" id="room2Width" name="room2Width" min="5" value="50" />

              <h2>Room 2 Height (ft.)</h2>
              <input type="number" id="room2Height" name="room2Height" min="5" value="50" />

              <h2>Room 2 Wall Color</h2>
              <input type="color" id="room2WallColor" name="room2WallColor" value="#222222" />

              <h2>Room 2 Floor Color</h2>
              <input type="color" id="room2FloorColor" name="room2FloorColor" value="#474747" />

              <h2>Door Placement on wall for room 2</h2>
              <select id="doorPlacement2" name="doorPlacement2">
                <option value="center">Front</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
                <option value="other">Back</option>
              </select>
            </div>

            <div id="thirdRoomInputs" style="display:none;">
              <h2>Room 3 Name</h2>
              <input type="text" id="room3Name" name="room3Name" value="Room 3" />

              <h2>Room 3 Width (ft.)</h2>
              <input type="number" id="room3Width" name="room3Width" min="5" value="50" />

              <h2>Room 3 Height (ft.)</h2>
              <input type="number" id="room3Height" name="room3Height" min="5" value="50" />

              <h2>Room 3 Wall Color</h2>
              <input type="color" id="room3WallColor" name="room3WallColor" value="#222222" />

              <h2>Room 3 Floor Color</h2>
              <input type="color" id="room3FloorColor" name="room3FloorColor" value="#474747" />

              <h2>Door Placement on wall for room 3</h2>
              <select id="doorPlacement3" name="doorPlacement3">
                <option value="center">Front</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
                <option value="other">Back</option>
              </select>
            </div>

            <div id="fourthRoomInputs" style="display:none;">
              <h2>Room 4 Name</h2>
              <input type="text" id="room4Name" name="room4Name" value="Room 4" />

              <h2>Room 4 Width (ft.)</h2>
              <input type="number" id="room4Width" name="room4Width" min="5" value="50" />

              <h2>Room 4 Height (ft.)</h2>
              <input type="number" id="room4Height" name="room4Height" min="5" value="50" />

              <h2>Room 4 Wall Color</h2>
              <input type="color" id="room4WallColor" name="room4WallColor" value="#222222" />

              <h2>Room 4 Floor Color</h2>
              <input type="color" id="room4FloorColor" name="room4FloorColor" value="#474747" />

              <h2>Window Options</h2>
              <label><input type="checkbox" id="windowBack" checked /> Back Window</label><br />
              <label><input type="checkbox" id="windowRight" checked /> Right Window</label><br />
              <label><input type="checkbox" id="windowLeft" checked /> Left Window</label><br />
              <label><input type="checkbox" id="windowFront" checked /> Front Window</label><br />
              <h2>Door Placement on wall for room 4</h2>
              <select id="doorPlacement4" name="doorPlacement4">
                <option value="center">Front</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
                <option value="other">Back</option>
              </select>
            </div>

            <h2>Furniture Options</h2>
            <label><input type="checkbox" name="furniture" value="bed" /> Bed</label>
            <input
              type="number"
              id="bedQuantity"
              name="bedQuantity"
              min="1"
              value="1"
              style="width: 50px;" />

            <!-- New inputs for bed width, height, and color -->
            <label for="bedWidth" style="margin-left: 10px;">Bed Width (units):</label>
            <input
              type="number"
              id="bedWidth"
              name="bedWidth"
              min="0.1"
              step="0.1"
              value="1.2"
              style="width: 80px;" />

            <label for="bedHeight" style="margin-left: 10px;">Bed Height (units):</label>
            <input
              type="number"
              id="bedHeight"
              name="bedHeight"
              min="0.1"
              step="0.1"
              value="1.5"
              style="width: 80px;" />

            <label for="bedColor" style="margin-left: 10px;">Bed Color:</label>
            <input
              type="color"
              id="bedColor"
              name="bedColor"
              value="#8B4513"
              style="width: 80px;" />

            <label><input type="checkbox" name="furniture" value="table" /> Table</label>
            <input
              type="number"
              id="tableQuantity"
              name="tableQuantity"
              min="1"
              value="1"
              style="width: 50px;" />

            <label><input type="checkbox" name="furniture" value="lights" /> Lights</label>
            <input
              type="number"
              id="lightsQuantity"
              name="lightsQuantity"
              min="1"
              value="1"
              style="width: 50px;" />
            <h2>Background Color</h2>
            <input type="color" id="backgroundColor" name="backgroundColor" value="#dde1e4" class="" />
          </aside>
          <main class="right-panel flex flex-col max-w- flex-1">

            <div class="flex px-4 py-3">

              <div id="container3D" class="w-full aspect-video rounded-xl bg-[#dde1e4]"></div>
            </div>
          </main>
        </div>
        <!-- Footer from index.html -->
        <footer class="mt-4 flex flex-col sm:flex-row items-center gap-4 px-4 sm:px-6 py-3">
          <button id="resetBtn" class="px-4 py-2 bg-gray-300 rounded w-full sm:w-auto">Reset</button>
          <select id="imageFormatSelector" class="border rounded px-2 py-1 w-full sm:w-auto">
            <option value="png">PNG</option>
            <option value="jpeg">JPG</option>
          </select>
          <button id="exportBtn" class="px-4 py-2 bg-blue-600 text-white rounded w-full sm:w-auto">Export as Image</button>
        </footer>
      </div>
    </div>
  </div>
  <!-- Notification div from index.html -->
  <div
    id="notification"
    style="position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.7); color: white; padding: 10px 20px; border-radius: 5px; display: none; z-index: 1000; font-family: Arial, sans-serif; font-size: 14px;"></div>

</body>

</html>