<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Manrope%3Awght%40400%3B500%3B700%3B800&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

  <title>Casahue</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
  <div style='font-family: Manrope, "Noto Sans", sans-serif;'>
    <header class="flex flex-col sm:flex-row items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f1f2f4] px-4 sm:px-10 py-3">
      <div class="flex items-center gap-4 text-[#121517]">
        <a href="/index.php" class="flex items-center space-x-2">
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
        class="flex min-w-[60px] sm:min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f1f2f4] text-[#121517] text-sm font-bold leading-normal tracking-[0.015em]">
        <a class="truncate" href="login.php">Login</a>
      </button>
      <button
        class="flex min-w-[60px] sm:min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#4e92ca] text-white text-sm font-bold leading-normal tracking-[0.015em]">
        <a class="truncate" href="createaccount.php">Sign Up</a>
      </button>
      </div>
    </header>


    <div class="px-4 sm:px-40 flex flex-1 justify-center py-5">
      <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
        <div class="@container">
          <div class="flex flex-col gap-6 px-4 py-10 @[480px]:gap-8 @[864px]:flex-row">
            <div
              class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg @[480px]:h-auto @[480px]:min-w-[300px] @[864px]:w-full"
              style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6HIz1lFik3Srhx-jocSftgG7XnhSypF2J8VKQAQfGA46fvNZlce27UA85wM1fZci0he3p_eR_WYZ3fYljfAqK2kMZaiq1QvdLZxxPkmku1sCcB8fxuYiR800SVpriIqnDXaJcni8Hnreb2br6Q_n6buJjNALFhy14p2s9hLQ4KcLXx2d-VSwqQs4QZQqPhx1O8_P4iny29tt60sd9C68RxLaUE574TSOmvku1ELY1q6yJU7E0dckogXd_g0LAYsfyZA3izA3aaQ-U");'></div>
            <div class="flex flex-col gap-6 @[480px]:min-w-[300px] @[480px]:gap-8 @[864px]:justify-center">
              <div class="flex flex-col gap-2 text-left">
                <h1
                  class="text-[#111518] text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                  Design Your Dream Room in 3D
                </h1>
                <h2 class="text-[#111518] text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">
                  Bring your vision to life with our intuitive 3D room design tool. Create, customize, and visualize your perfect space with ease.
                </h2>
              </div>
              <button
                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-[#2a95ed] text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em]">
                <a class="truncate" href="login.php">Start Designing</a>
              </button>
            </div>
          </div>
        </div>
        <h2 class="text-[#111518] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">How It Works</h2>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4">
          <div class="flex flex-1 gap-3 rounded-lg border border-[#dbe1e6] bg-white p-4 flex-col">
            <div class="text-[#111518]" data-icon="House" data-size="24px" data-weight="regular">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                <path
                  d="M218.83,103.77l-80-75.48a1.14,1.14,0,0,1-.11-.11,16,16,0,0,0-21.53,0l-.11.11L37.17,103.77A16,16,0,0,0,32,115.55V208a16,16,0,0,0,16,16H96a16,16,0,0,0,16-16V160h32v48a16,16,0,0,0,16,16h48a16,16,0,0,0,16-16V115.55A16,16,0,0,0,218.83,103.77ZM208,208H160V160a16,16,0,0,0-16-16H112a16,16,0,0,0-16,16v48H48V115.55l.11-.1L128,40l79.9,75.43.11.1Z"></path>
              </svg>
            </div>
            <div class="flex flex-col gap-1">
              <h2 class="text-[#111518] text-base font-bold leading-tight">1. Choose Your Room</h2>
              <p class="text-[#617789] text-sm font-normal leading-normal">Select your room type and dimensions to get started.</p>
            </div>
          </div>
          <div class="flex flex-1 gap-3 rounded-lg border border-[#dbe1e6] bg-white p-4 flex-col">
            <div class="text-[#111518]" data-icon="PencilSimple" data-size="24px" data-weight="regular">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                <path
                  d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM92.69,208H48V163.31l88-88L180.69,120ZM192,108.68,147.31,64l24-24L216,84.68Z"></path>
              </svg>
            </div>
            <div class="flex flex-col gap-1">
              <h2 class="text-[#111518] text-base font-bold leading-tight">2. Design and Decorate</h2>
              <p class="text-[#617789] text-sm font-normal leading-normal">Add furniture, decorations, and finishes to personalize your space.</p>
            </div>
          </div>
          <div class="flex flex-1 gap-3 rounded-lg border border-[#dbe1e6] bg-white p-4 flex-col">
            <div class="text-[#111518]" data-icon="Eye" data-size="24px" data-weight="regular">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                <path
                  d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"></path>
              </svg>
            </div>
            <div class="flex flex-col gap-1">
              <h2 class="text-[#111518] text-base font-bold leading-tight">3. View in 3D</h2>
              <p class="text-[#617789] text-sm font-normal leading-normal">Explore your design in stunning 3D and make adjustments as needed.</p>
            </div>
          </div>
        </div>
        <h2 class="text-[#111518] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Featured Designs</h2>
        <div class="flex overflow-y-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden">
          <div class="flex items-stretch p-4 gap-3">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-lg min-w-60">
              <div
                class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg flex flex-col"
                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBGCp_wEmFvaVHtma1AfsDX9j2HNLM5YmAxjKv6fwp2B1iXDDuzGgZsBlZhnRFlCU--SsB19tCQ7t3Lf3TeVos4z6Q6tZAg8JL-1Aeafs6DPPqegd266xTgS2-cBvRsl1JBW0wFhdFoByO5SyxXf-0MaOis91XYf8UbvUHHLST7bU9Z5cZ9fpIPeEBeRKdEa7ROCKSUg_y4yQLsgz2TFwBq9Wu5-ytS5NMbWclT3H2hiZo3S6D9ZSpUA_znaKVoovz9JgGtQ7KY5-Xx");'></div>
              <div>
                <p class="text-[#111518] text-base font-medium leading-normal">Modern Living Room</p>
                <p class="text-[#617789] text-sm font-normal leading-normal">A spacious living room with a neutral color palette and modern furniture.</p>
              </div>
            </div>
            <div class="flex h-full flex-1 flex-col gap-4 rounded-lg min-w-60">
              <div
                class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg flex flex-col"
                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDK3LF0oI3RgAAmbnSAzmKZBnEFVI_yQmmMlRworzoI1kjkgQbbLkKwt_3KCDm1kcU61-Pm2kNkzFkftJNyDDHvRDm72V5Hi3sPllk3r8aa4NR4ILRmyh3eOIGj7ahoXKw7t70m2-kpu0xmZ_8Vp49ul8mugeAE9q-HtEN3phx2nKYVm_tUNBvRJmtU0q5Kko5_M_cNMNYIv17_iOebKDG69J_YAPkTzM6pum-S9bnouAE7RQ9EnvWJ3VxBgU5etLi5HI7amelowO8h");'></div>
              <div>
                <p class="text-[#111518] text-base font-medium leading-normal">Contemporary Bedroom</p>
                <p class="text-[#617789] text-sm font-normal leading-normal">A cozy bedroom with a large bed, soft lighting, and stylish decor.</p>
              </div>
            </div>
            <div class="flex h-full flex-1 flex-col gap-4 rounded-lg min-w-60">
              <div
                class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg flex flex-col"
                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB3FNRbaQXDmcLt0oAYOzmeC6lMShRaacCKzVWneUNdMRZDkYoLImc1Q-VTIaWcMuiMtvObmRoeCzBW53eW2oSQiqGWY3wpoF-PbcoHScvqXC4PACvjqHq_lcD_ycnNvj1jUzXDcQlRmBz66r3dWm9XkaP_I-P-0KSQnnWx6mNib1r3nZmX-CujdkqAkj9T6ClUkFcWhnTjWXdJ2lEQ4yennW2vG0-jIYBtU02w-sk4BYtGgf-bxO1KEylNKnKjxsBtrNpCD0UasGe7");'></div>
              <div>
                <p class="text-[#111518] text-base font-medium leading-normal">Minimalist Kitchen</p>
                <p class="text-[#617789] text-sm font-normal leading-normal">A sleek kitchen with clean lines, stainless steel appliances, and ample storage.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="@container">
          <div class="flex flex-col justify-end gap-6 px-4 py-10 @[480px]:gap-8 @[480px]:px-10 @[480px]:py-20">
            <div class="flex flex-col gap-2 text-center">
              <h1
                class="text-[#111518] tracking-light text-[32px] font-bold leading-tight @[480px]:text-4xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em] max-w-[720px]">
                Ready to Create Your Dream Room?
              </h1>
            </div>
            <div class="flex flex-1 justify-center">
              <div class="flex justify-center">
                <button
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-[#2a95ed] text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] grow">
                  <a class="truncate" href="login.php">Start Designing</a>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</body>


</html>