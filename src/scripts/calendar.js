const url = new URL(window.location.href);
const params = url.searchParams;
const d = params.get('d');
const ttl = document.getElementById("ttl");
const group = document.getElementById("group");
const startAt = document.getElementById("startAt");
const endAt = document.getElementById("endAt");
const bgClr = document.getElementsByName("bgClr");
const fontClr = document.getElementsByName("fontClr");
const text = document.getElementById("text");
const addBtn = document.getElementById("addBtn");
const errTtl = document.getElementById("errTtl");
const errGroup = document.getElementById("errGroup");
const errSpan = document.getElementById("errSpan");
const errClr = document.getElementById("errClr");
const errText = document.getElementById("errText");
const displayAtViewer = document.getElementById("displayAtViewer");
const displaySpanContainer = document.getElementById("displaySpanContainer");
const displaySpan = document.getElementById("displaySpan");
const displayGroup = document.getElementById("displayGroup");
const selectGroups = document.getElementsByName("selectGroups");
const calendar = document.getElementById("calendar");
const appFormContainer = document.getElementById("appFormContainer");
const grayBackAppForm = document.getElementById("grayBackAppForm");
const displaySelectGroup = document.getElementById("displaySelectGroup");
const days = ["日", "月", "火", "水", "木", "金", "土"];
let visitingTabNum = 0;
let span = "month";
let displayAt = new Date();
// displayAt.setDate( displayAt.getDate() - 1 );
let isDisplaySelectGroup = false;
let groupList = ["self"];
let beforeWidth = document.body.clientWidth;
let isOpenAppForm = false;
displayAt.setHours( displayAt.getHours() + 9 );
startAt.value = displayAt.toISOString().slice(0, 16);
endAt.value = displayAt.toISOString().slice(0, 16);

if( d ) {
  displayAt.setFullYear( d.slice(0, 4) );
  displayAt.setMonth( Number( d.slice(5, 7) ) - 1 );
  displayAt.setDate( d.slice(8, 10) );
}

changeDisplayAt( null );
getGroupList();

if( document.body.clientWidth < 960 ) {
  span = "plan";
  
  displaySpanContainer.style.display = "none";
  changeDisplayAt( null );
  displayCalendar();
} else if( document.body.clientWidth >= 960 && beforeWidth < 960 ) {
  displaySpanContainer.style.display = "flex";
}

window.addEventListener("resize", () => {
  if( document.body.clientWidth < 960 && beforeWidth >= 960 ) {
    span = "plan";
    
    displaySpanContainer.style.display = "none";
    changeDisplayAt( null );
    displayCalendar();
  } else if( document.body.clientWidth >= 960 && beforeWidth < 960 ) {
    displaySpanContainer.style.display = "flex";
  }

  beforeWidth = document.body.clientWidth;
});

function getGroupList() {
  group.innerHTML = '<option value="self">自分のみ</option>';
  displayGroup.innerHTML = '\
    <input type="checkbox" id="self" name="selectGroups" value="self" onchange="changeDisplayGroup()" checked>\
    <label for="self">\
      <div class="ttl">自分のみ</div>\
    </label>\
  ';
  groupList = ["self"];
  fetch('https://nk-apps.net/src/php/getGroupListByLoginToken.php')
    .then(response => response.json())
    .then(res => {
      for( let i=0; i<res.length; i++ ) {
        group.innerHTML += '<option value="' + res[i][0] + '">' + res[i][2] + '</option>';
        displayGroup.innerHTML += '\
          <input type="checkbox" id="displayGroup' + res[i][0] + '" name="selectGroups" value="' + res[i][0] + '" onchange="changeDisplayGroup()" checked>\
          <label for="displayGroup' + res[i][0] + '">\
            <div class="ttl">' + res[i][2] + '</div>\
          </label>\
        ';
        groupList.push( res[i][0] );
      }
      displaySelectGroup.textContent = groupList.length + "件選択中";
    })

  displayCalendar();
}

function checkCompleteForm() {
  if( ttl.value === "" ||
      group.value === "" ||
      startAt.value === "" ||
      endAt.value === "" ) {
    addBtn.disabled = true;
  } else {
    addBtn.disabled = false;
  }
}

function addPlan() {  
  const startAtDate = new Date(startAt.value);
  const endAtDate = new Date(endAt.value);
  let selectBgClr = "";
  let selectFontClr = "";
  errTtl.textContent = "";
  errGroup.textContent = "";
  errSpan.textContent = "";
  errClr.textContent = "";
  errText.textContent = "";

  for( let i=0; i<bgClr.length; i++ ) {
    if( bgClr[i].checked === true ) {
      selectBgClr = bgClr[i].value;
    }
  }
  
  for( let i=0; i<fontClr.length; i++ ) {
    if( fontClr[i].checked === true ) {
      selectFontClr = fontClr[i].value;
    }
  }

  if( ttl.value === "" ) errTtl.textContent = "タイトルを入力してください。";
  if( group.value === "" ) errGroup.textContent = "グループを選択してください。";
  if( startAtDate > endAtDate ) errSpan.textContent = "終了時刻は開始時刻以降に設定してください。";
  if( endAt.value === "" ) errSpan.textContent = "終了日時を入力してください。";
  if( startAt.value === "" ) errSpan.textContent = "開始日時を入力してください。";
  if( selectFontClr === "" ) errClr.textContent = "文字色を選択してください。";
  if( selectBgClr === "" ) errClr.textContent = "背景色を選択してください。";
  if( errTtl.textContent === "" &&
      errGroup.textContent === "" &&
      errSpan.textContent === "" &&
      errClr.textContent === "" &&
      errText.textContent === "" ) {
    const eventDatas = [
      ttl.value,
      group.value,
      startAt.value,
      endAt.value,
      selectBgClr,
      selectFontClr,
      text.value
    ];
    fetch('https://nk-apps.net/src/php/addPlan.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify(eventDatas)
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExitGroup":
            alert("グループ情報取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
            break;
          case "success":
            alert(ttl.value + "を追加しました。");
            ttl.value = "";
            text.value = "";

            displayCalendar();
            break;
        }
      })
  }
}

function changeDisplayAt( mode ) {
  // Calc date.
  switch( mode ) {
    case "before":
      switch ( span ) {
        case "date":
          displayAt.setDate( displayAt.getDate() - 1 );
          break;
        case "week":
          displayAt.setDate( displayAt.getDate() - 7 );
          break;
        case "plan":
        case "month":
          displayAt.setMonth( displayAt.getMonth() - 1 );
          break;
      }
      break;
    case "after":
      switch ( span ) {
        case "date":
          displayAt.setDate( displayAt.getDate() + 1 );
          break;
        case "week":
          displayAt.setDate( displayAt.getDate() + 7 );
          break;
        case "plan":
        case "month":
          displayAt.setMonth( displayAt.getMonth() + 1 );
          break;
      }
      break;
  }

  // Display date.
  switch ( span ) {
    case "date":
      displayAtViewer.textContent = displayAt.getFullYear() + "年" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "月" + displayAt.getDate().toString().padStart(2, '0') + "日(" + days[displayAt.getDay()] + ")";
      break;
    case "week":
      let displayStart = new Date( displayAt.getTime() );
      displayStart.setDate( displayStart.getDate() - Number(displayStart.getDay() ) );
      let displayEnd = new Date( displayStart.getTime() );
      displayEnd.setDate( displayEnd.getDate() + 6 );
      displayAtViewer.textContent = displayStart.getFullYear() + "/" + ( displayStart.getMonth() + 1 ).toString().padStart(2, '0') + "/" + displayStart.getDate().toString().padStart(2, '0') + "(日) ～ " + ( displayEnd.getMonth() + 1 ).toString().padStart(2, '0') + "/" + displayEnd.getDate().toString().padStart(2, '0') + "(土)";
      break;
    case "plan":
    case "month":
      displayAtViewer.textContent = displayAt.getFullYear() + "年" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "月";
      break;
  }

  if( mode ) {
    displayCalendar();
  }
}

function changeSpan() {
  span = displaySpan.value;

  changeDisplayAt( null );
  displayCalendar();
}

function displayCalendar() {
  console.clear();
  let isOclock;
  let planLeftMargin;
  let displayMonth;
  let startDate;
  let endDate;
  let planDate;
  let displayDate;
  let planDatas = [];
  // let loopTtl;
  let datePlanCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
  let weekPlanCount = [
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 
                        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                      ];

  calendar.innerHTML = "";

  calendar.classList.remove("plan");
  calendar.classList.remove("date");
  calendar.classList.remove("week");
  calendar.classList.remove("month");

  switch( span ) {
    case "plan":
      calendar.classList.add("plan");

      startDate = new Date( displayAt.getTime() );
      startDate.setDate( 1 );
      endDate = new Date( startDate.getTime() );
      endDate.setMonth( endDate.getMonth() - 1 );
      endDate.setDate( -1 );
      displayDate = new Date( startDate.getTime() );

      for( let i=0; i<=endDate.getDate()+1; i++ ) {
        planDatas[i] = [];
      }
      
      // display plan block container
      calendar.innerHTML += '<div class="plan-block-container" id="planBlockContainer"></div>';
      const planBlockContainer = document.getElementById("planBlockContainer");

      // get and display event datas
      displayMonth = displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0');

      fetch('https://nk-apps.net/src/php/getPlanDatasForPlan.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( displayMonth )
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          for( let i=0; i<res[1].length; i++ ) {
            if( groupList.includes( res[1][i][2] ) ) {
              let planStartDate = new Date( res[1][i][6] );
              planStartDate.setHours( 0 );
              planStartDate.setMinutes( 0 );
              let planEndDate = new Date( res[1][i][7] );
              planEndDate.setHours( 0 );
              planEndDate.setMinutes( 0 );
              planDate = new Date( planStartDate );
              console.log("----------");
              console.log(res[1][i][3]);
              let loopDates = ( ( planEndDate - planStartDate ) / 86400000 ) + 1;
              for( let loopDate=1; loopDate<=loopDates; loopDate++ ) {
                // loopTtl = "";
                // if( loopDates != 1 ) {
                //   loopTtl = '[' + loopDate + '/' + loopDates + '] ';
                // }
                
                console.log("now:" + displayMonth);
                console.log(( planStartDate.getFullYear() + "-" + ( planStartDate.getMonth() + 1 ).toString().padStart(2, '0') ))
                console.log( Number(res[1][i][6].slice(8, 10) ) + loopDate - 1 );
                if( planDatas.length > ( ( Number(res[1][i][6].slice(8, 10) ) ) + loopDate - 1 ) % ( endDate.getDate() + 1 ) && displayMonth === ( planStartDate.getFullYear() + "-" + ( planStartDate.getMonth() + 1 ).toString().padStart(2, '0') ) ) {
                  planDatas[( ( Number(res[1][i][6].slice(8, 10) ) ) + loopDate - 1 ) % ( endDate.getDate() + 2 )].push(res[1][i]);
                  // planDatas[Number(res[1][i][6].slice(8, 10)) + loopDate - 1][planDatas[Number(res[1][i][6].slice(8, 10)) + loopDate - 1].length-1][3] = loopTtl + planDatas[Number(res[1][i][6].slice(8, 10)) + loopDate - 1][planDatas[Number(res[1][i][6].slice(8, 10)) + loopDate - 1].length-1][3];
                }
                
                planStartDate.setDate( planStartDate.getDate() + 1 );
              }
            }
          }
          
          let planCount = 0;
          for( let i=1; i<=endDate.getDate()+1; i++ ) {
            planBlockContainer.innerHTML += '<div class="row" id="planDate' + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + '-' + displayDate.getDate().toString().padStart(2, '0') + '"></div>';
            
            if( planDatas[i].length !== 0 ) {
              if( planCount === 0 ) {
                document.getElementById("planDate" + ( displayDate.getMonth() + 1 ).toString().padStart(2, "0") + "-" + displayDate.getDate().toString().padStart(2, "0")).innerHTML += '<div class="sub-ttl" style="margin-top: 0;">' + ( displayDate.getMonth() + 1 ) + "月" + displayDate.getDate() + '日(' + days[displayDate.getDay()] + ')</div>';
              } else {
                document.getElementById("planDate" + ( displayDate.getMonth() + 1 ).toString().padStart(2, "0") + "-" + displayDate.getDate().toString().padStart(2, "0")).innerHTML += '<div class="sub-ttl" >' + ( displayDate.getMonth() + 1 ) + "月" + displayDate.getDate() + '日(' + days[displayDate.getDay()] + ')</div>';
              }
            }

            for( let j=0; j<planDatas[i].length; j++ ) {
              planBlockContainer.innerHTML += '\
                <div class="plan" style="background: ' + planDatas[i][j][4] + '; color: ' + planDatas[i][j][5] + ';" onclick="displayModal(\'plan-detail\', \'' + planDatas[i][j][1] + '\')">\
                  <div class="ttl">' + planDatas[i][j][3] + '</div>\
                  <div class="group-name">' + planDatas[i][j][9] + '</div>\
                </div>\
              ';
              planCount++;
            }

            displayDate.setDate( displayDate.getDate() + 1 );
          }
          console.log(planDatas);
        })
        .catch(error => {
          console.log(error);
          alert("予定情報の取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
        });

        console.log("planDate" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + '-' + displayAt.getDate().toString().padStart(2, '0'));
      break;
    case "date":
      calendar.classList.add("date");

      startDate = new Date( displayAt.getTime() );
      startDate.setHours( 0 );
      startDate.setMinutes( 0 );
      startDate.setSeconds( 0 );
      endDate = new Date( startDate.getTime() );
      endDate.setHours( 23 );
      endDate.setMinutes( 59 );
      endDate.setSeconds( 59 );

      // display time line
      calendar.innerHTML += '<div class="date-block-container" id="dateBlockContainerDate"></div>';
      const dateBlockContainerDate = document.getElementById("dateBlockContainerDate");
      dateBlockContainerDate.innerHTML += '<div class="time-container" id="timeContainerDate"></div>';
      const timeContainerDate = document.getElementById("timeContainerDate");
      for( let i=0; i<24; i++ ) {
        timeContainerDate.innerHTML += '<div class="time">' + i + '</div>';
      }

      // display date block
      dateBlockContainerDate.innerHTML += '\
        <div class="date-block">\
          <div class="plan-container" id="planContainerDate' + displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0') + '"></div>\
        </div>\
      ';

      // get and display event datas
      fetch('https://nk-apps.net/src/php/getPlanDatasForDate.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayAt.getDate().toString().padStart(2, '0') )
      })
        .then(response => response.json())
        .then(res => {
          for( let i=0; i<res[1].length; i++ ) {
            if( groupList.includes( res[1][i][2] ) ) {
              let planHeight;
              let planStartDate = new Date( res[1][i][6] );
              planStartDate.setHours( 0 );
              planStartDate.setMinutes( 0 );
              let planEndDate = new Date( res[1][i][7] );
              planEndDate.setHours( 0 );
              planEndDate.setMinutes( 0 );
              let loopDates = ( ( planEndDate - planStartDate ) / 86400000 ) + 1;
              displayDate = new Date( res[1][i][6].slice(0, 10) );
              for( let loopDate=1; loopDate<=loopDates; loopDate++ ) {
                if( loopDates == 1 ) {
                  // calc height
                  let startTime = new Date( res[1][i][6] );
                  let endTime = new Date( res[1][i][7] );
                  planHeight = Math.max( ( ( endTime - startTime ) / 28000 ), 32 );

                  // If display span include this plan, display  it
                  if( startDate < startTime && endTime < endDate ) {
                    // calc top
                    planTop = ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 );
                    
                    // set plan count and margin
                    planLeftMargin = 0;
                    if( endTime.getMinutes() === 0 ) {
                      isOclock = -1;
                    } else {
                      isOclock = 0;
                    }
                    for( let j=startTime.getHours(); j<=endTime.getHours() + isOclock; j++ ) {
                      if( datePlanCount[j] > planLeftMargin ) {
                        planLeftMargin = datePlanCount[j];
                      }
                      datePlanCount[j]++;
                    }
              
                    if( document.getElementById("planContainerDate" + res[1][i][6].slice(0, 10)) ) {
                      document.getElementById("planContainerDate" + res[1][i][6].slice(0, 10)).innerHTML += '\
                        <div class="plan" style="height: ' + planHeight + 'px; margin-top: ' + planTop + 'px; margin-left: ' + ( planLeftMargin * 248 ) + 'px; background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                          <div class="ttl">' + res[1][i][3] + '</div>\
                        </div>\
                      ';
                    }
                  }
                } else {
                  // calc height
                  let startTime = new Date( res[1][i][6] );
                  let endTime = new Date( res[1][i][7] );
                  let checkIsDisplayPlan = new Date( startTime.getTime() );
                  checkIsDisplayPlan.setDate( checkIsDisplayPlan.getDate() + loopDate - 1 );

                  if( startDate < checkIsDisplayPlan && checkIsDisplayPlan < endDate ){
                    if( loopDate === 1 ) {
                      // start date
                      planHeight = ( 128 * 24 ) - Math.max( ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 ), 32 );
                      
                      // set plan count
                      planLeftMargin = 0;
                      for( let j=startTime.getHours(); j<24; j++ ) {
                        if( datePlanCount[j] > planLeftMargin ) {
                          planLeftMargin = datePlanCount[j];
                        }
                        datePlanCount[j]++;
                      }
                    } else if( loopDate === loopDates ) {
                      // end date
                      planHeight = Math.max( ( endTime.getHours() * 128 ) + ( endTime.getMinutes() / 60 * 128 ), 32 );
                      
                      // set plan count
                      planLeftMargin = 0;
                      if( endTime.getMinutes() === 0 ) {
                        isOclock = -1;
                      } else {
                        isOclock = 0;
                      }
                      for( let j=0; j<endTime.getHours() + isOclock; j++ ) {
                        if( datePlanCount[j] > planLeftMargin ) {
                          planLeftMargin = datePlanCount[j];
                        }
                        datePlanCount[j]++;
                      }
                    } else {
                      // other date
                      planHeight = 128 * 24;
                      
                      // set plan count
                      planLeftMargin = 0;
                      for( let j=0; j<24; j++ ) {
                        if( datePlanCount[j] > planLeftMargin ) {
                          planLeftMargin = datePlanCount[j];
                        }
                        datePlanCount[j]++;
                      }
                    }

                    // calc top
                    if( loopDate !== 1 ) {
                      planTop = 0;
                    } else {
                      planTop = ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 );
                    }
                    
                    if( document.getElementById("planContainerDate" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ) ) {
                      document.getElementById("planContainerDate" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ).innerHTML += '\
                        <div class="plan" style="height: ' + planHeight + 'px; margin-top: ' + planTop + 'px; margin-left: ' + ( planLeftMargin * 248 ) + 'px; background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                          <div class="ttl">[' + loopDate + '/' + loopDates + '] ' + res[1][i][3] + '</div>\
                        </div>\
                      ';
                    }
                  }
                  displayDate.setDate( displayDate.getDate() + 1 );
                }
              }
            }
          }
          console.log(datePlanCount);
        })
        .catch(error => {
          console.log(error);
          alert("予定情報の取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
        });
      break;
    case "week":
      calendar.classList.add("week");

      startDate = new Date( displayAt.getTime() );
      startDate.setDate( startDate.getDate() - startDate.getDay() );
      startDate.setHours( 0 );
      startDate.setMinutes( 0 );
      startDate.setSeconds( 0 );
      endDate = new Date( startDate.getTime() );
      endDate.setDate( endDate.getDate() + 6 );
      endDate.setHours( 23 );
      endDate.setMinutes( 59 );
      endDate.setSeconds( 59 );

      // display date num container
      calendar.innerHTML += '<div class="date-num-container" id="dateNumContainerWeek"></div>';
      dateNumContainerWeek.innerHTML = '<div class="time-space"></div>';
      displayDate = new Date( startDate.getTime() );
      const displayWeekStart = displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0');
      for( let i=0; i<7; i++ ) {
        dateNumContainerWeek.innerHTML += '\
          <div class="date-num">' + displayDate.getDate() + '日(' + days[i] + ')</div>\
        ';
        displayDate.setDate( displayDate.getDate() + 1 );
      }
      const displayWeekEnd = displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0');

      // display time line
      calendar.innerHTML += '<div class="date-block-container" id="dateBlockContainerWeek"></div>';
      const dateBlockContainerWeek = document.getElementById("dateBlockContainerWeek");
      dateBlockContainerWeek.innerHTML += '<div class="time-container" id="timeContainerWeek"></div>';
      const timeContainerWeek = document.getElementById("timeContainerWeek");
      for( let i=0; i<24; i++ ) {
        timeContainerWeek.innerHTML += '<div class="time">' + i + '</div>';
      }

      // display date block
      displayDate = new Date( startDate.getTime() );
      for( let i=0; i<7; i++ ) {
        dateBlockContainerWeek.innerHTML += '\
          <div class="date-block">\
            <div class="plan-container" id="planContainerWeek' + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') + '"></div>\
          </div>\
        ';

        displayDate.setDate( displayDate.getDate() + 1 );
      }

      // get and display event datas
      fetch('https://nk-apps.net/src/php/getPlanDatasForWeek.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( [displayWeekStart, displayWeekEnd] )
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          for( let i=0; i<res[1].length; i++ ) {
            if( groupList.includes( res[1][i][2] ) ) {
              let planHeight;
              let planStartDate = new Date( res[1][i][6] );
              planStartDate.setHours( 0 );
              planStartDate.setMinutes( 0 );
              let planEndDate = new Date( res[1][i][7] );
              planEndDate.setHours( 0 );
              planEndDate.setMinutes( 0 );
              console.log("----------");
              console.log(res[1][i][3]);
              let loopDates = ( ( planEndDate - planStartDate ) / 86400000 ) + 1;
              displayDate = new Date( res[1][i][6].slice(0, 10) );
              for( let loopDate=1; loopDate<=loopDates; loopDate++ ) {
                if( loopDates == 1 ) {
                  // calc height
                  let startTime = new Date( res[1][i][6] );
                  let endTime = new Date( res[1][i][7] );
                  planHeight = Math.max( ( ( endTime - startTime ) / 28000 ), 32 );

                  // If display span include this plan, display  it
                  if( startDate < startTime && endTime < endDate ) {
                    // calc top
                    planTop = ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 );
                    
                    // set plan count and margin
                    planLeftMargin = 0;
                    if( endTime.getMinutes() === 0 ) {
                      isOclock = -1;
                    } else {
                      isOclock = 0;
                    }
                    for( let j=startTime.getHours(); j<=endTime.getHours() + isOclock; j++ ) {
                      if( weekPlanCount[startTime.getDay()][j] > planLeftMargin ) {
                        planLeftMargin = weekPlanCount[startTime.getDay()][j];
                      }
                      weekPlanCount[startTime.getDay()][j]++;
                    }
              
                    if( document.getElementById("planContainerWeek" + res[1][i][6].slice(0, 10)) ) {
                      document.getElementById("planContainerWeek" + res[1][i][6].slice(0, 10)).innerHTML += '\
                        <div class="plan" style="height: ' + planHeight + 'px; margin-top: ' + planTop + 'px; margin-left: ' + ( planLeftMargin * 108 ) + 'px; background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                          <div class="ttl">' + res[1][i][3] + '</div>\
                        </div>\
                      ';
                    }
                  }
                } else {
                  // calc height
                  let startTime = new Date( res[1][i][6] );
                  let endTime = new Date( res[1][i][7] );
                  let checkIsDisplayPlan = new Date( startTime.getTime() );
                  checkIsDisplayPlan.setDate( checkIsDisplayPlan.getDate() + loopDate - 1 );

                  if( startDate < checkIsDisplayPlan && checkIsDisplayPlan < endDate ){
                    if( loopDate === 1 ) {
                      // start date
                      planHeight = ( 128 * 24 ) - Math.max( ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 ), 32 );
                      
                      // set plan count
                      planLeftMargin = 0;
                      for( let j=startTime.getHours(); j<24; j++ ) {
                        if( weekPlanCount[startTime.getDay()][j] > planLeftMargin ) {
                          planLeftMargin = weekPlanCount[startTime.getDay()][j];
                        }
                        weekPlanCount[startTime.getDay()][j]++;
                      }
                    } else if( loopDate === loopDates ) {
                      // end date
                      planHeight = Math.max( ( endTime.getHours() * 128 ) + ( endTime.getMinutes() / 60 * 128 ), 32 );
                      
                      // set plan count
                      planLeftMargin = 0;
                      if( endTime.getMinutes() === 0 ) {
                        isOclock = -1;
                      } else {
                        isOclock = 0;
                      }
                      for( let j=0; j<endTime.getHours() + isOclock; j++ ) {
                        if( weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j] > planLeftMargin ) {
                          planLeftMargin = weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j];
                        }
                        weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j]++;
                      }
                    } else {
                      // other date
                      planHeight = 128 * 24;
                      
                      // set plan count
                      planLeftMargin = 0;
                      for( let j=0; j<24; j++ ) {
                        if( weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j] > planLeftMargin ) {
                          planLeftMargin = weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j];
                        }
                        weekPlanCount[( startTime.getDay() + loopDate - 1 ) % 7][j]++;
                      }
                    }

                    // calc top
                    if( loopDate !== 1 ) {
                      planTop = 0;
                    } else {
                      planTop = ( startTime.getHours() * 128 ) + ( startTime.getMinutes() / 60 * 128 );
                    }
                    
                    if( document.getElementById("planContainerWeek" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ) ) {
                      document.getElementById("planContainerWeek" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ).innerHTML += '\
                        <div class="plan" style="height: ' + planHeight + 'px; margin-top: ' + planTop + 'px; margin-left: ' + ( planLeftMargin * 108 ) + 'px; background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                          <div class="ttl">[' + loopDate + '/' + loopDates + '] ' + res[1][i][3] + '</div>\
                        </div>\
                      ';
                    }
                  }
                  displayDate.setDate( displayDate.getDate() + 1 );
                }
              }
            }
          }
        })
        .catch(error => {
          console.log(error);
          alert("予定情報の取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
        });
      break;
    case "month":
      calendar.classList.add("month");

      startDate = new Date( displayAt.getTime() );
      startDate.setDate( 1 );
      endDate = new Date( startDate.getTime() );
      endDate.setMonth( endDate.getMonth() - 1 );
      endDate.setDate( -1 );

      // display day
      calendar.innerHTML += '<div class="day-container" id="dayContainerMonth"></div>';
      const dayContainerMonth = document.getElementById("dayContainerMonth");
      for( let i=0; i<7; i++ ) {
        dayContainerMonth.innerHTML += '\
          <div class="day">' + days[i] + '</div>\
        ';
      }
      
      // display before space
      calendar.innerHTML += '<div class="date-block-container" id="dateBlockContainerMonth"></div>';
      const dateBlockContainerMonth = document.getElementById("dateBlockContainerMonth");
      for( let i=0; i<startDate.getDay(); i++ ) {
        dateBlockContainerMonth.innerHTML += '<div class="space"></div>';
      }

      // week count
      let weekCount = 5;
      if( ( startDate.getDay() === 0 ) && ( ( endDate.getDate() + 1 ) === 28 ) ) {
        weekCount = 4;
      } else if ( ( startDate.getDay() === 5 ) && ( ( endDate.getDate() + 1 ) === 31 ) ||
                  ( startDate.getDay() === 6 ) && ( ( endDate.getDate() + 1 ) >= 30 ) ) {
        weekCount = 6;
      }

      // display date block
      for( let i=1; i<=(endDate.getDate()+1); i++ ) {
        dateBlockContainerMonth.innerHTML += '\
          <div class="date-block" style="height: calc( 100% / ' + weekCount + ' );">\
            <div class="date-num">' + i + '</div>\
            <div class="plan-container" id="planContainerMonth' + displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0') + "-" + i.toString().padStart(2, '0') + '"></div>\
            <div class="data-container"></div>\
          </div>\
        ';
      }

      // get and display event datas
      displayMonth = displayAt.getFullYear() + "-" + ( displayAt.getMonth() + 1 ).toString().padStart(2, '0');
      fetch('https://nk-apps.net/src/php/getPlanDatasForMonth.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( displayMonth )
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          for( let i=0; i<res[1].length; i++ ) {
            if( groupList.includes( res[1][i][2] ) ) {
              let planStartDate = new Date( res[1][i][6] );
              planStartDate.setHours( 0 );
              planStartDate.setMinutes( 0 );
              let planEndDate = new Date( res[1][i][7] );
              planEndDate.setHours( 0 );
              planEndDate.setMinutes( 0 );
              console.log("----------");
              console.log(res[1][i][3]);
              let loopDates = ( ( planEndDate - planStartDate ) / 86400000 ) + 1;
              displayDate = new Date( res[1][i][6].slice(0, 10) );
              for( let loopDate=1; loopDate<=loopDates; loopDate++ ) {
                if( loopDates == 1 ) {
                  document.getElementById("planContainerMonth" + res[1][i][6].slice(0, 10)).innerHTML += '\
                    <div class="plan" style="background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                      <div class="ttl">' + res[1][i][3] + '</div>\
                    </div>\
                  ';
                } else {
                  console.log( displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') );
                  
                  if( document.getElementById("planContainerMonth" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ) ) {
                    document.getElementById("planContainerMonth" + displayDate.getFullYear() + "-" + ( displayDate.getMonth() + 1 ).toString().padStart(2, '0') + "-" + displayDate.getDate().toString().padStart(2, '0') ).innerHTML += '\
                      <div class="plan" style="background: ' + res[1][i][4] + '; color: ' + res[1][i][5] + ';" onclick="displayModal(\'plan-detail\', \'' + res[1][i][1] + '\')">\
                        <div class="ttl">[' + loopDate + '/' + loopDates + '] ' + res[1][i][3] + '</div>\
                      </div>\
                    ';
                  }
                  displayDate.setDate( displayDate.getDate() + 1 );
                }
              }
            }
          }
        })
        .catch(error => {
          alert("予定情報の取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
        });
      break;
  }
}

function displaySelectGroupForm() {
  if( isDisplaySelectGroup ) {
    displayGroup.style.display = "none";
    unstyledBack.style.display = "none";
    isDisplaySelectGroup = false;
  } else {
    displayGroup.style.display = "block";
    unstyledBack.style.display = "block";
    isDisplaySelectGroup = true;
  }
}

function changeDisplayGroup() {
  groupList = [];

  for( let i=0; i<selectGroups.length; i++ ) {
    if( selectGroups[i].checked === true ) {
      groupList.push( selectGroups[i].value );
    }
  }

  displaySelectGroup.textContent = groupList.length + "件選択中";
  displayCalendar();
}

function displayModal( mode, id ) {
  grayBack.style.display = "block";
  modal.style.display = "block";
  modal.style.animation = "displayModal 0.5s ease forwards";
  modalContent.style.animation = "displayModalContent 1.0s ease forwards";
  grayBack.style.display = "displayGrayBack 0.5s ease forwards";

  switch( mode ) {
    case "plan-detail":
      modalTtl.textContent = "予定詳細";

      // Get plan details
      fetch('https://nk-apps.net/src/php/getPlanAllDatasFromPlanId.php', {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify( id )
      })
        .then(response => response.json())
        .then(res => {
          console.log(res);
          modalContent.innerHTML = '\
            <div class="top">\
              <div class="basic-info">\
                <div class="ttl">' + res[1][3] + '</div>\
                <div class="span">' + res[1][6].slice(0, 16) + ' ～ ' + res[1][7].slice(0, 16) + '</div>\
                <div class="text">' + res[1][8] + '</div>\
              </div>\
              <div class="btn-container">\
                <a href="https://nk-apps.net/edit-plan/?pid=' + res[1][1] + '">\
                  <button class="m secondary" id="editBtn" style="margin-bottom: var(--margin-xxs);">予定情報を編集</button>\
                </a>\
                <button class="m danger-secondary" id="deleteBtn" onclick="deletePlan(\'' + res[1][1] + '\',\'' + res[1][3] + '\')">予定を削除</button>\
              </div>\
            </div>\
            \
            <div class="normal-tab-container">\
              <div class="btns" id="planDetailTabBtns">\
                <button class="btn visiting" onclick="changeTab(0)">詳細</button>\
                <button class="btn" onclick="changeTab(1)">メンバー</button>\
              </div>\
              <div class="contents" id="planDetailTabContents">\
                <div class="content visiting">\
                  <table class="detail-table">\
                    <tr>\
                      <th>タイトル</th>\
                      <td>' + res[1][3] + '</td>\
                    </tr>\
                    <tr>\
                      <th>開始日時</th>\
                      <td>' + res[1][6].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>終了日時</th>\
                      <td>' + res[1][7].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>グループ</th>\
                      <td>' + res[1][13] + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成日時</th>\
                      <td>' + res[1][9].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>作成者</th>\
                      <td>' + res[1][14] + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新日時</th>\
                      <td>' + res[1][10].slice(0, 16) + '</td>\
                    </tr>\
                    <tr>\
                      <th>最終更新者</th>\
                      <td>' + res[1][15] + '</td>\
                    </tr>\
                  </table>\
                </div>\
                <div class="content">\
                  <table class="user-table" id="userTable">\
                  </table>\
                </div>\
              </div>\
            </div>\
          ';
          visitingTabNum = 0;

          const userTable = document.getElementById("userTable");
          userTable.innerHTML = "";
          if( res[1][2] === "self" ) {
            userTable.innerHTML = '<div class="no-data">この予定は他のユーザには公開されません。</div>';
          } else {
            for( let i=0; i<res[2].length; i++ ) {
              userTable.innerHTML += '\
                <tr>\
                  <td>\
                    <div class="icon">\
                      <img src="https://nk-apps.net/src/images/user-icons/' + res[2][i][0] + '/' + res[2][i][3] + '" alt="ユーザアイコン" width="40px" height="40px" onerror="this.src=\'https://nk-apps.net/src/images/no_user_images.svg\'" />\
                    </div>\
                    <div class="name">' + res[2][i][2] + '</div>\
                    <div class="id">ID: ' + res[2][i][1] + '</div>\
                  </td>\
                </tr>\
              ';
            }
          }

          if( res[1][16] == "member" ) {
            document.getElementById("editBtn").disabled = true;
            document.getElementById("deleteBtn").disabled = true;
          }
        })
        .catch(error => {
          console.log(error);
          alert("予定情報の取得中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
          closeModal();
        });
      break;
  }
}

function closeModal() {
  modal.style.animation = "closeModal 1.0s ease forwards";
  modalContent.style.animation = "closeModalContent 0.5s ease forwards";
  grayBack.style.display = "closeGrayBack 0.5s ease forwards";
  grayBack.style.display = "none";
}

function changeTab( tabNum ) {
  const planDetailTabContents = document.getElementById("planDetailTabContents");
  const planDetailTabBtns = document.getElementById("planDetailTabBtns");

  planDetailTabBtns.children[visitingTabNum].classList.remove("visiting");
  planDetailTabBtns.children[tabNum].classList.add("visiting");
  planDetailTabContents.children[visitingTabNum].classList.remove("visiting");
  planDetailTabContents.children[tabNum].classList.add("visiting");

  visitingTabNum = tabNum;
}

function deletePlan( planId, planTtl ) {
  if( confirm( planTtl + "を削除します。\nよろしいですか？") ) {
    fetch('https://nk-apps.net/src/php/deletePlan.php', {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify( planId )
    })
      .then(response => response.json())
      .then(res => {
        switch( res ) {
          case "notExistPlan":
            alert("予定の削除処理中に予期せぬエラーが発生しました。\nお手数ですが、時間をおいて再度お試しください。");
            break;
          case "cannotDeletePlan":
            alert("予定削除の権限がありません。\n予定がグループで共有されている場合、作成者か管理者である必要があります。");
            break;
          case "success":
            alert(planTtl + "を削除しました。");
            break;
        }
      })
    
    closeModal();
    displayCalendar();
  }
}

function displayAppForm() {
  grayBackAppForm.style.display = "block";
  appFormContainer.style.display = "block";
  appFormContainer.style.animation = "displayAppForm 0.5s ease forwards";
  grayBackAppForm.style.display = "displayGrayBack 0.5s ease forwards";
}

function closeAppForm() {
  appFormContainer.style.animation = "closeAppForm 1.0s ease forwards";
  grayBackAppForm.style.display = "closeGrayBack 0.5s ease forwards";
  grayBackAppForm.style.display = "none";
}