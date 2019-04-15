jQuery(document).ready(function () {
  var body = $('body');

  /**
   * View More버튼을 lightbox에 추가한다
   * 이는 메인 페이지의 lightbox에만 적용된다
   */
  function addViewMoreBtn() {
    
  }
  if (body.hasClass('home')) {
    addViewMoreBtn();   
  }
});