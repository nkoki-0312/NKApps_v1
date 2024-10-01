<div class="unstyled-back" id="unstyledBack" onclick="displaySelectGroupForm()"></div>
<div class="unstyled-back" id="noticeBack" onclick="closeNotice()"></div>
<div class="gray-back" id="grayBack" onclick="closeModal()"></div>
<div class="gray-back" id="grayBackAppForm" onclick="closeAppForm()"></div>

<div class="modal" id="modal">
  <div class="top-info">
    <div class="ttl" id="modalTtl"></div>
    <button class="m unstyled close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="content" id="modalContent">

  </div>
</div>

<header>
  <a href="https://nk-apps.net/portal/">
    <div class="logo">
      <img src="https://nk-apps.net/src/images/icon.svg" alt="NKAppsのロゴ">
    </div>
  </a>
  <div class="menu">
    <button class="m unstyled" onclick="displayModal('asset_cat', '')">資産カテゴリー</button>
    <button class="m unstyled" onclick="displayModal('bop_cat', '')">収支カテゴリー</button>
  </div>
  <div class="notice">
    <button class="unstyled m notice-btn" onclick="displayNotice()">
      <div class="icon"><i class="fa-regular fa-bell"></i></div>
      <div class="counter" id="noticeCounter"></div>
    </button>
  </div>
  <div class="account">
    <a href="https://nk-apps.net/mypage/">
      <button class="m unstyled" style="padding-left: 8px;">
        <img src="https://nk-apps.net/src/images/user-icons/<?php echo $userDatas[0]; ?>/<?php echo $userDatas[5]; ?>" alt="ユーザアイコン" style="width: 40px; height: 40px;" class="user-icon"  onerror="this.src='https://nk-apps.net/src/images/no_user_images.svg'" />
        <div class="user-name"><?php echo $userDatas[2]; ?></div>
      </button>
    </a>
  </div>
  <button class="h-menu-btn" id="hMenuBtn" onclick="moveHMenu();"><i class="fa-solid fa-bars"></i></button>
</header>
<div class="notice-list-container" id="noticeListContainer">
  <div class="notice-list" id="noticeList">
  </div>
</div>
<div class="header-space"></div>
<div class="gray-back" id="grayBackHMenu" onclick="moveHMenu();"></div>
<div class="h-menu" id="hMenu">
  <div class="cat-btn-container">
    <button class="m unstyled" onclick="displayModal('asset_cat', '')">資産カテゴリー</button>
    <button class="m unstyled" onclick="displayModal('bop_cat', '')">収支カテゴリー</button>
  </div>

  <div class="link-container">
    <a href="https://nk-apps.net/calendar/">
      <button>カレンダー</button>
    </a>
    <a href="https://nk-apps.net/todo/">
      <button>ToDoリスト</button>
    </a>
    <a href="https://nk-apps.net/kakeibo/">
      <button>家計簿</button>
    </a>
  </div>
  
  <div class="notice-list-container-h-menu">
    <div class="notice-ttl">お知らせ</div>
    <div class="notice-list-h-menu" id="noticeListHMenu"></div>
  </div>

  <div class="account">
    <a href="https://nk-apps.net/mypage/">
      <button class="m unstyled" style="padding-left: 8px;">
        <img src="https://nk-apps.net/src/images/user-icons/<?php echo $userDatas[0]; ?>/<?php echo $userDatas[5]; ?>" alt="ユーザアイコン" style="width: 40px; height: 40px;" class="user-icon"  onerror="this.src='https://nk-apps.net/src/images/no_user_images.svg'" />
        <div class="user-name"><?php echo $userDatas[2]; ?></div>
      </button>
    </a>
  </div>
</div>