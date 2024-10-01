<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>家計簿 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/kakeibo.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
  <script src="https://unpkg.com/chartjs-plugin-colorschemes"></script>
</head>
<body>
  <div class="unstyled-back" id="unstyledBack" onclick="displaySelectGroupForm()"></div>
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

  <?php include('../src/includes/kakeibo-header.php') ?>

  <div class="single-block-large">
    <!-- APP FORM -->
    <button class="m primary open-form-btn" onclick="displayAppForm()">取引を追加</button>
    <div class="app-form-container" id="appFormContainer">
      <div class="ttl-container">
        <div class="ttl">取引を追加</div>
        <button class="m unstyled close-btn" onclick="closeAppForm()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="normal-tab-container">
        <div class="btns" id="appFormTabBtns">
          <button class="btn visiting" onclick="changeTab(0)">支出</button>
          <button class="btn" onclick="changeTab(1)">収入</button>
          <button class="btn" onclick="changeTab(2)">移動</button>
        </div>
        <div class="contents" id="appFormTabContents">
          <!-- EXPENSE -->
          <div class="content visiting">
            <div class="form">
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>金額</div>
                <div class="container-flex">
                  <input type="number" class="short-form-amount" id="expenseAmount" onchange="checkCompleteForm('expense')">
                  <div class="sub-text">円</div>
                </div>
                <div class="err" id="errExpenseAmount"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>日付</div>
                <input type="date" id="expenseDate" onchange="checkCompleteForm('expense')">
                <div class="err" id="errExpenseDate"></div>
                <div class="form-explanation"></div>
              </div>

              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>資産カテゴリー</div>
                <select id="expenseAssetCat" onchange="checkCompleteForm('expense')"></select>
                <div class="err" id="errExpenseAssetCat"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>収支カテゴリー</div>
                <select id="expenseBopCat" onchange="checkCompleteForm('expense')"></select>
                <div class="err" id="errExpenseBopCat"></div>
                <div class="form-explanation"></div>
              </div>

              <div class="row">
                <div class="ttl">詳細</div>
                <textarea id="expenseText" onchange="checkCompleteForm('expense')"></textarea>
                <div class="err" id="errExpenseText"></div>
                <div class="form-explanation"></div>
              </div>
            </div>
            <div class="container-center">
              <button class="m primary" id="addExpenseBtn" onclick="addExpense()" disabled>追加</button>
            </div>
          </div>
        
          <!-- INCOME -->
          <div class="content">
            <div class="form">
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>金額</div>
                <div class="container-flex">
                  <input type="number" class="short-form-amount" id="incomeAmount" onchange="checkCompleteForm('income')">
                  <div class="sub-text">円</div>
                </div>
                <div class="err" id="errIncomeAmount"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>日付</div>
                <input type="date" id="incomeDate" onchange="checkCompleteForm('income')">
                <div class="err" id="errIncomeDate"></div>
                <div class="form-explanation"></div>
              </div>

              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>資産カテゴリー</div>
                <select id="incomeAssetCat" onchange="checkCompleteForm('income')"></select>
                <div class="err" id="errIncomeAssetCat"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>収支カテゴリー</div>
                <select id="incomeBopCat" onchange="checkCompleteForm('income')"></select>
                <div class="err" id="errIncomeBopCat"></div>
                <div class="form-explanation"></div>
              </div>

              <div class="row">
                <div class="ttl">詳細</div>
                <textarea id="incomeText" onchange="checkCompleteForm('income')"></textarea>
                <div class="err" id="errIncomeText"></div>
                <div class="form-explanation"></div>
              </div>
            </div>
            <div class="container-center">
              <button class="m primary" id="addIncomeBtn" onclick="addIncome()" disabled>追加</button>
            </div>
          </div>
        
          <!-- TRANSFER -->
          <div class="content">
            <div class="form">
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>金額</div>
                <div class="container-flex">
                  <input type="number" class="short-form-amount" id="transferAmount" onchange="checkCompleteForm('transfer')">
                  <div class="sub-text">円</div>
                </div>
                <div class="err" id="errTransferAmount"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>日付</div>
                <input type="date" id="transferDate" onchange="checkCompleteForm('transfer')">
                <div class="err" id="errTransferDate"></div>
                <div class="form-explanation"></div>
              </div>
              
              <div class="row">
                <div class="ttl"><span class="must-form" title="必須項目">*</span>資産カテゴリー</div>
                <div class="container-flex">
                  <select class="short-form-asset-cat" id="transferAssetCatFrom" onchange="checkCompleteForm('transfer')"></select>
                  <div class="sub-text sub-text-asset-cat">から</div>
                </div>
                <div class="container-flex">
                  <select class="short-form-asset-cat" id="transferAssetCatTo" onchange="checkCompleteForm('transfer')"></select>
                  <div class="sub-text sub-text-asset-cat">へ</div>
                </div>
                <div class="err" id="errTransferAssetCat"></div>
                <div class="form-explanation"></div>
              </div>

              <div class="row">
                <div class="ttl">詳細</div>
                <textarea id="transferText" onchange="checkCompleteForm('transfer')"></textarea>
                <div class="err" id="errTransferText"></div>
                <div class="form-explanation"></div>
              </div>
            </div>
            <div class="container-center">
              <button class="m primary" id="addTransferBtn" onclick="addTransfer()" disabled>移動</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main">
      <!-- SELECT FORM -->
      <div class="select-form">
        <div class="row-left">
          <button class="s unstyled" onclick="changeDisplayAt('before')"><i class="fa-solid fa-angle-left"></i></button>
          <div id="displayAtViewer">2024年05月</div>
          <button class="s unstyled" onclick="changeDisplayAt('after')"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <div class="row row-right">
          <div class="ttl">グループ</div>
          <select id="selectGroup" onchange="changeGroup()">
            <option value="self">自分のみ</option>
          </select>
        </div>
        <div class="row row-right" id="displaySpanContainer">
          <div class="ttl">期間</div>
          <select id="displaySpan" onchange="changeSpan()">
          <option value="all">すべて</option>
          <option value="year">年毎</option>
          <option value="month" selected>月毎</option>
          <option value="date">日毎</option>
          </select>
        </div>
      </div>

      <div class="charts" id="charts">
        <div class="summary-container">
          <div class="content income">
            <div class="ttl">支出</div>
            <div class="amount" id="summaryExpense"></div>
          </div>
          <div class="content expense">
            <div class="ttl">収入</div>
            <div class="amount" id="summaryIncome"></div>
          </div>
          <div class="content diff">
            <div class="ttl">差異</div>
            <div class="amount" id="summaryDiff"></div>
          </div>
          <div class="content total">
            <div class="ttl">総資産</div>
            <div class="amount" id="summaryTotal"></div>
          </div>
        </div>
        
        <div class="main-chart-container">
          <div class="ctrl-bar">
            <div class="row row-right">
              <div class="ttl">表示</div>
              <select id="summaryMode" onchange="changeSummaryMode()">
                <option value="all">収入・収支</option>
                <option value="income">収入</option>
                <option value="expense">支出</option>
              </select>
            </div>
          </div>
          <div class="chart-container">
            <canvas id="summary"></canvas>
            <div class="no-datas" id="summaryNoDatas">データが登録されていません。</div>
          </div>
        </div>

        <div class="double-charts">
          <div class="pie-chart-container">
            <div class="chart-container" id="pieChartContainer">
              <div class="ternary-ttl">支出</div>
              <div class="expense-chart">
                <canvas id="pieChartExpense"></canvas>
                <div class="no-datas" id="pieChartNoExpenseDatas">データが登録されていません。</div>
              </div>
              <div class="ternary-ttl">収入</div>
              <div class="income-chart">
                <canvas id="pieChartIncome"></canvas>
                <div class="no-datas" id="pieChartNoIncomeDatas">データが登録されていません。</div>
              </div>
            </div>
          </div>

          <div class="bop-list-container">
            <div class="ctrl-bar">
              <div class="row row-right">
                <div class="ttl">表示</div>
                <select id="listMode" onchange="changeListMode()">
                  <option value="expense" checked>支出</option>
                  <option value="income">収入</option>
                  <option value="transfer">移動</option>
                </select>
              </div>
            </div>
            <div id="bopList"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="mouseCircle"></div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/kakeibo.js"></script>
</body>
</html>