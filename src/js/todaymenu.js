/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */ 

 $.get("/response.todaymenu.php").done(function(data) {
     if (data.length > 2) {
         console.log(data);
         loadMenu(data);
     }
 });


 function loadMenu(menu) {
     var parsedMenu = $('<div/>').append(menu);
     var tabPane = $(".tab-pane").first();
     var date = new Date();
     var currentTime = date.getHours() * 60 + date.getMinutes();

     // 1학사, 2학사, 하늘샘 (조중석)
     $(parsedMenu).find('table').each(function(index, value) {

         var menuTitles = [];
         var menuData = [];
         var menuIndex = 0;

         // 시간을 파싱하여 해당하는 메뉴 가져오기

         $(this).find('thead tr').first().find('th').each(function(i, v) {
             if (i == 0) return;
             menuTitles.push(v);
             var time = v.innerHTML.split('-')[1].split(')')[0].split(':');
             var termEnd = parseInt(time[0]) * 60 + parseInt(time[1]);
             if (currentTime >= termEnd)
                 menuIndex = (++menuIndex) % 3;
         });

         // 식당 메뉴 종류 보여주기

         $(this).find('tbody tr').each(function(i) {
             $(this).find('td').each(function(j, v) {

                 if (j == 0) {
                     menuData.push(v);
                 }

                 // 해당하는 시간의 메뉴만 표시
                 else if (j == menuIndex + 1) {
                     menuData.push(v);
                 }
             })
         });

         // 테이블 그리기

         var table = $('<table/>').addClass('table').addClass('table-condensed').addClass('table-hover');
         var title = $('<div/>').append(menuTitles[menuIndex].innerHTML);
         table.css('margin-bottom', '0px');
         table.css('margin-top', '5px');
         table.css('font-size', '14px');
         title.css('margin-top', '13px');
         title.css('margin-left', '4px');
         title.css('font-size', '16px');

         var tableBody = $('<tbody/>');
         table.append(tableBody);

         for (var i = 0; i < menuData.length; i += 2) {
             tableBody.append($('<tr/>').append(menuData[i]).append(menuData[i + 1]));
         }

         tabPane.append(title);
         tabPane.append(table);

         tabPane = tabPane.next();
     });
 }