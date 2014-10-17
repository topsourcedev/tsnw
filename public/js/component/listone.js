/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function listone_forw(page,obj){
    page = Number(page);
    var currentPage = $(obj).parents(".pageNav").siblings("#listone_page_"+page);
    currentPage.hide();
    var nextPage = $(obj).parents(".pageNav").siblings("#listone_page_"+(page+1));
    nextPage.show();
    var currentPageNav = $(obj).parents(".pageNav");
    currentPageNav.hide();
    var nextPageNav = currentPageNav.siblings("#pageNav_"+(page+1));
    nextPageNav.show();
}
function listone_back(page,obj){
    page = Number(page);
    var currentPage = $(obj).parents(".pageNav").siblings("#listone_page_"+page);
    currentPage.hide();
    var nextPage = $(obj).parents(".pageNav").siblings("#listone_page_"+(page-1));
    nextPage.show();
    var currentPageNav = $(obj).parents(".pageNav");
    currentPageNav.hide();
    var nextPageNav = currentPageNav.siblings("#pageNav_"+(page-1));
    nextPageNav.show();
}