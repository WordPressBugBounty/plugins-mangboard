=== Mang Board WP===
Contributors: kitae-park
Donate link: http://www.mangboard.com/donate/
Tags: board,gallery,calendar,mangboard,망보드
Requires at least: 4.0.0
Tested up to: 6.6
Requires PHP: 5.5.1
Stable tag: 1.8.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MangBoard is bulletin board for your community website. (Gallery,Calendar,Forum, Form,FAQ)

== Description ==

The plugin is available in English, Japanese(日本語), Chinese(中国語) and Korean(한국어).

**Mang Board WP**

* Mang Board WP is a plugin formed of Wordpress BBS providing a variety of features such as storage BBS, Gallery BBS, Calendar BBS, membership management, analytics, ecommerce, social login, social sharing, and SEO (Search Engine Optimization)  

**Mang Board Features**

* Allow you to easily keep up with rapidly changing technology and platforms 
* Allow you to easily customize architecture you want to build 
* Allow you to build the responsive web design for various devices such as desktop, mobile and tablets. 
* Allow you to add much functionality with plugins

**Mang Board Functionalities** 

* MB-BASIC: PDS (Personal Data Storage), Gallery, Calendar, Q&A, Webzine (Web Magazine), FAQ
* MB-BUSINESS: Registration, Social login, membership management, Social sharing, SEO (Search Engine Optimization)
* MB-COMMERCE: ecommerce sites built using responsive design, open market features, points/rewards and coupon features.

**Mang Board 특징 (Korean)**

* 빠르게 변화하는 기술, 플랫폼에 보다 쉽게 대응
* 커스터마이징을 위한 게시판으로 구조를 쉽게 변형이 가능
* 데스크탑, 태블릿, 모바일 등 다양한 디바이스에 맞는 반응형웹 구축 가능
* 플러그인 기능을 통해 다양한 기능을 손쉽게 추가 가능
* 다른 한국형 게시판(kboard,kingkong board)과 혼합해서 사용 가능

**Mang Board 기능 (Korean)**

* MB-BASIC: 자료실, 갤러리, 캘린더, 문의하기, 웹진, 자주묻는질문 게시판
* MB-BUSINESS: 회원가입, 소셜 로그인, 회원정보, 회원관리, 소셜 공유, 검색엔진 최적화(SEO)
* MB-COMMERCE: 반응형 쇼핑몰, 오픈마켓, 포인트, 쿠폰, 상품관리, 카트, 주문, 결제

**Mang Board Support**

* Homepage: [https://www.mangboard.com](https://www.mangboard.com)
* Store: [https://www.mangboard.com/store/](https://www.mangboard.com/store/)
* Demo: [http://demo.mangboard.com](http://demo.mangboard.com)
* Manual: [http://www.mangboard.com/manual/](http://www.mangboard.com/manual/)

== Installation ==

**Mang Board Installation (English)**

* Upload the entire "mangboard" folder to the "/wp-content/plugins/" directory.
* Activate the plugin through the 'Plugins' menu in WordPress.


**Mang Board Installation (Korean)**

* 플러그인 압축파일을 다운로드 받아 워드프레스 “/wp-content/plugins” 폴더에 업로드 합니다
* “/wp-content/plugins/mangboard” 폴더가 보이시면 워드프레스 설치된 플러그인 목록에 나타납니다
* 워드프레스 관리자 화면에서 “플러그인>설치된 플러그인” 목록에서 “Mang Board WP” 플러그인을 찾아 활성화 버튼을 클릭합니다
* 왼쪽에 있는 워드프레스 관리자 메뉴에서 “Mang Board” 메뉴가 보이시면 설치가 정상적으로 완료 되었습니다


== Frequently Asked Questions ==

= Mang Board License =

* Licensed under the GPLv2 license (http://www.gnu.org/licenses/gpl-2.0.html)

= Mang Board Minimum Requirements =

* PHP version 5.4.0 or greater (PHP 7.3 or greater is recommended)
* MySQL version 5.0 or greater / MariaDB version 10.0 or greater (UTF-8)
* WordPress 4.0 or greater

= Mang Board 게시판을 추가하려면 어떻게 해야 하나요? =

*  “Mangboard>게시판 관리” 메뉴를 클릭하고 “게시판 추가” 버튼을 클릭합니다
*  게시판 이름을 입력하고 기타 게시판 옵션들을 설정하고 “확인” 버튼을 클릭해서 게시판을 추가합니다 (게시판 이름만 필수 입력)
*  게시판 목록에 추가된 게시판의 이름과 워드프레스 페이지에 추가할 수 있는 Shortcode 가 나타납니다
*  원하는 형태의 Shortcode를 복사한 다음 관리자 메뉴 “페이지>새 페이지 추가” 메뉴를 클릭합니다
*  페이지 제목을 입력하고 복사한 Shortcode를 에디터 텍스트 영역에 복사한 다음 “공개하기” 버튼을 클릭하면 망보드 게시판이 워드프레스 페이지에 등록됩니다
*  등록된 페이지를 홈페이지 메뉴에 추가합니다

= Mang Board User Level =

* Nonmember : Level 0
* Member : Level 1~10
* Admin : Level 10


== Screenshots == 

1. Mang Board > Board
2. Mang Board > Gallery
3. Mang Board > Calendar
4. Mang Board > Webzine
5. Mang Board > Frequently Asked Questions
6. Mang Board > Form

== Upgrade Notice ==

= 2.2.0 =
안정화 작업 및 보안 기능 수정

== Changelog ==

= 2.2.9 (1.8.4) =
* 에디터에서 video 태그를 추가할 수 있도록 htmlpurifier 보안설정 수정
* CK 에디터4 설정파일 수정
* minor bug 수정

= 2.2.8 =
* 관리자 회원(레벨 10)이 게시물 작성시 HTML 모드에서 <script> 태그를 추가해서 게시물 작성이 가능하도록 수정
  => 일반 회원(레벨 9 이하)이 게시물 작성시 <script> 태그를 추가할 경우에는 보안기능에 의해 제거되거나 인코딩되어 텍스트로 표시됨
* minor bug 수정

= 2.2.7 =
* 모바일 캘린더 게시판에서 span 태그가 출력되는 버그 수정
* Astra 테마에서 발생하는 호환성 문제 수정 (CSS 코드 추가)
* minor bug 수정

= 2.2.6 (1.8.3) =
* HTMLPurifier 버전 업데이트 (4.14.0 => 4.15.0)
* CK 에디터4 지원중단에 따른 로딩방식 변경 (CDN => 내부로딩)
* 에디터 컴포저 > 동영상 입력 기능 수정
* minor bug 수정

= 2.2.5 (1.8.2) =
* 보안 기능 수정 [패치스택(Patchstack)에서 보안점검을 통해 알려주신 보안사항 수정]
  => 망보드를 최신버전으로 업데이트 해주는 기능에 보안 코드(wp_verify_nonce) 추가
* 글보기 페이지 해시태그 기능에 URL 보안검사 함수(esc_url)가 잘 못 적용되어 해시태그 앞에 http 텍스트가 추가되는 버그 수정
   => 망보드 2.2.3 ~ 2.2.4 버전에서만 발생하는 버그

= 2.2.4 (1.8.1) =
* 보안 취약점 수정 [패치스택(Patchstack)에서 보안점검을 통해 알려주신 XSS 보안 취약점 수정]
   => 관리자가 악성 스크립트가 삽입되어 있는 유해 사이트 접속시 취약점으로 이용 가능 
       관리자가 크롬 및 사파리 브라우저 사용시 브라우저에서 제공하는 XSS 보안 기능 때문에 취약점으로 이용 불가
* 폰트 플러그인을 지원하기 위한 코드 추가
   => 스마트 에디터2 및 CK 에디터 내부에서 웹폰트를 사용할 수 있게 해주는 폰트 플러그인 준비중

= 2.2.3 (1.8.0) =
* 보안 기능을 강화하기 위한 보안 코드(escape,prepare) 추가
* 망보드 호환성 향상을 위한 CSS 코드 수정

= 2.2.2 (1.7.9) =
* 워드프레스에서 DISALLOW_UNFILTERED_HTML 설정이 true로 설정되어 있을 경우 관리자도 게시물 등록시 스크립트를 사용할 수 없도록 수정
* 글보기 페이지 하단 > 이전,현재글,다음글 기능에서 현재글 보여주기 기능 삭제
* 모델링 및 템플릿 확장 기능 수정
* minor bug 수정

= 2.2.1 =
* 워드프레스 6.4 버전에서 발생하는 호환성 문제 수정
* 모델링 및 템플릿 확장 기능 수정
* minor bug 수정

= 2.2.0 =
* 파라미터(GET,POST) 필터 기능 수정
* CK 에디터 버전 업데이트 (4.15.1 => 4.22.1)
* minor bug 수정

= 2.1.9 =
* 일부 워드프레스 환경에서 자동생성되는 P 태그가 자바스크립트 내부에 들어가지 않도록 2.1.8 버전에서 추가된 코드 제거
  => 파일 업로드 플러그인을 사용하는 환경에서 글쓰기 오류가 발생하는 문제가 확인되어 추가된 코드 제거

= 2.1.8 =
* 일부 테마(Twenty Twenty-Two,Twenty Twenty-Three)에서 게시판 검색 기능이 동작하지 않는 문제 수정
* 일부 워드프레스 환경에서 자동생성되는 P 태그가 자바스크립트 내부에 들어가지 않도록 코드 수정
* 최적화 및 안정성 개선을 위한 코드 수정
* minor bug 수정

= 2.1.7 =
* 일부 페이지 빌더(Oxygen Builder)에서 게시물 등록이 안되는 문제 수정
* 슬라이드 위젯에서 join 속성 사용시 게시물로 연결 안되는 버그 수정
* 스팸 방지 보안기능 설정 옵션(사용/사용안함) 추가
  => 일부환경에서 오류가 발생하는 문제 때문에 스팸방지 보안기능을 사용안함으로 설정할 수 있는 옵션 추가
* minor bug 수정

= 2.1.6 =
* 최근 게시물 위젯에서 워드프레스 포스트를 불러올 수 있는 확장 기능 추가 (최근 게시물 위젯, 슬라이드 위젯, 타이핑 위젯)
* 대시보드 데이터 로딩 방식 수정(XML=>JSON)
* 최적화 및 안정성 개선을 위한 코드 수정
* minor bug 수정

= 2.1.5 =
* 기본 테마(Twenty Twenty-Two,Twenty Twenty-Three)에서 발생하는 호환성 문제 수정
* 일부 호스팅 환경(해외호스팅)에서 파일 업로드 안되는 버그 수정
* 최적화 및 안정성 개선을 위한 코드 수정
* minor bug 수정

= 2.1.4 =
* 엘리멘터 플러그인에서 "텍스트 에디터" 아이템을 이용하여 게시판 추가시 에러 메시지 출력
  => 엘리멘터에서 제공하는 "숏코드" 아이템을 이용해서만 게시판 추가 가능
* 호환성 문제가 있는 테마에서 게시판 사용시 에러 메시지 출력
  => 호환성 문제가 있는 테마: Twenty Twenty-Two,Twenty Twenty-Three
* 모델링 및 템플릿 확장 기능 수정
* 망보드 호환성 향상을 위한 CSS 코드 수정

= 2.1.3 =
* 글쓰기 폼에서 필수 항목 미입력시 모션으로 스크롤 이동 후 포커스 적용되는 기능 추가
* post_id 숏코드 속성이 캘린더 및 갤러리 스킨 제목에 적용되지 않는 버그 수정
* minor bug 수정

= 2.1.2 =
* 댓글 기능에서 회원 레벨 아이콘 표시되도록 수정
* 스토어 연결 설치기능 > 설치된 상품을 삭제할 수 있는 기능 추가
* 망보드 호환성 향상을 위한 CSS 코드 수정

= 2.1.1 =
* 관리자 페이지 언어 변경 기능 추가
* 다국어 기능 수정 및 최적화
* SEO 메타 태그 출력시 CSS 스타일 제거 기능 추가
* 망보드 호환성 향상을 위한 CSS 코드 수정
* 템플릿 Class 기능 지원을 위한 게시판 숏코드 속성 추가
   [mb_board name="board1" 디바이스_게시판모드_class="템플릿 Class"]
   - 디바이스 : desktop, tablet, mobile
   - 게시판모드 : list, view, write
   => 모바일에서 글목록 화면 접속시 클래스 추가
     [mb_board name="board1" mobile_list_class="mb-class1 mb-class2"]
   => 모바일 접속시 클래스 추가
     [mb_board name="board1" mobile_class="mb-class1 mb-class3"]
   => 글목록 화면 접속시 클래스 추가
     [mb_board name="board1" list_class="mb-class1 mb-class4"]
   => 모든 게시판 페이지에 클래스 추가
     [mb_board name="board1" class="mb-class1 mb-class5"]
* minor bug 수정

= 2.1.0 =
* 일부 설치환경(서버 직접설치)에서 파일 업로드 안되는 버그 수정
* 워드프레스 6.0 > 스마트 에디터2로 작성된 일부 게시물에서 테두리가 생기는 CSS 문제 수정
* 업로드 파일 보안 체크 기능 수정
* minor bug 수정

= 2.0.9 =
* 데이타 보안 필터 기능 수정
* mac OS 로컬 서버환경에서 사용시 정상적으로 동작하지 않는 문제 수정
* minor bug 수정

= 2.0.8 =
* 반응형 기능 수정 및 반응형 클래스 추가 (mb-mobile-landscape, mb-mobile-portrait, mb-tablet-landscape, mb-tablet-portrait)
* 다국어 언어(한국어,일본어,중국어,영어) 패키지 기능 수정
* 에디터 컴포저 > 동영상 입력 기능 수정
* minor bug 수정

= 2.0.7 =
* 모델링 및 템플릿 확장 기능 수정
* 브라우저 소스보기 기능에서 망보드 설치버전이 노출되지 않도록 수정
* minor bug 수정

= 2.0.6 =
* 보안 취약점 수정 [KISA에서 보안점검을 통해 알려주신 취약점 수정]
   => 포스트/페이지 작성 권한이 있는 회원만 취약점 접근 가능
* IE 브라우저에서 삽입된 이미지 크기 변경시 모바일에서 이미지 높이가 비율에 맞게 줄어들지 않는 버그 수정
* 모델링 및 템플릿 확장 기능 수정

= 2.0.5 =
* 워드프레스 5.9 버전에서 망보드 대시보드 페이지 레이아웃이 깨지는 문제 수정
* HTMLPurifier 버전 업데이트 (4.12.0->4.14.0)
* 망보드 모바일앱 패키지 연동 최적화 작업
* minor bug 수정

= 2.0.4 =
* 보안 취약점 수정 [KISA에서 보안점검을 통해 알려주신 취약점 수정]
   => 포스트/페이지 작성 권한이 있는 회원만 취약점 접근 가능
* 일부 환경에서 로그인 접속 로그가 저장되지 않는 버그 수정
* 반응형 레이아웃 기능 수정
* minor bug 수정

= 2.0.3 =
* 일부 호스팅 환경(해외호스팅, PHP 8.0)에서 게시글 등록이 안되는 문제 수정
* 접근권한 보안 기능 수정
* minor bug 수정

= 2.0.2 =
* 기본스킨(bbs_basic)에서 작성자/조회수를 삭제한 공지사항 스킨(bbs_notice_m1) 추가
* minor bug 수정

= 2.0.1 =
* 에디터 컴포저 > 동영상 입력 기능 추가 (반응형)
   => 입력된 동영상을 브라우저 크기에 최적화된 사이즈로 자동 설정
* minor bug 수정

= 2.0.0 =
* 보안 취약점 수정 [KISA에서 보안점검을 통해 알려주신 취약점 수정]
* 데이타 전송시 데이타 검증 기능 수정
* minor bug 수정

= 1.9.9 =
* 관리자>MangBoard>옵션설정>Naver, Google Site Verification 입력 추가
   => 검색엔진 등록시 사이트 등록 및 소유확인을 위한 Site Verification 설정
* 일부환경에서 CK에디터 표시되지 않는 버그 수정
* minor bug 수정

= 1.9.8 =
* 망보드 업데이트 기능 수정
* Iframe 허용 목록에 구글 문서,캘린더,드라이브 주소 추가
* minor bug 수정

= 1.9.7 =
* 일반 사용자가 에디터에서 유튜브 및 동영상 iframe 추가시 보안기능(htmlpurifier)에 의해 제거되는 버그 수정
* 망보드 대시보드에서 1회이상 망보드 업데이트를 진행했을 경우에만 STORE 연결 설치가 가능하도록 수정
* minor bug 수정

= 1.9.6 =
* 카카오(다음) 우편번호 검색 서비스 정책 변경에 따른 로딩방식 변경
  => 기존 로딩방식은 2021년 3월 31일 이후 이용이 중지됨
      수정파일: mangboard/includes/functions/func.util.php
* 검색 및 카테고리 설정시 글보기 페이지에서 이전,다음글 기능에 반영되지 않는 버그 수정(1.9.4~1.9.5버전)
* 스마트 에디터 기본 폰트 변경(돋움=>나눔고딕)
* 알림 팝업창 상단 배경색 변경(파랑=>검정) 및 CSS 수정

= 1.9.5 =
* 게시판 설정에서 관리 권한(기본 8레벨) 항목에 설정된 레벨 이상인 회원이 공지사항을 등록할 수 있도록 수정
* 게시판 추가시 이름에 영문 대문자가 포함되어 있으면 자동으로 소문자로 변경되도록 수정
* CK 에디터 4.7.3 => 4.15.1 버전 업데이트 및 툴바 수정
* 모델링 및 템플릿 확장 기능 수정
* minor bug 수정

= 1.9.4 =
* 웹진 및 갤러리 게시판 디폴트 이미지 숏코드 속성 추가
   - gallery_image_type : image(대표 이미지가 등록된 게시물만 목록에 표시), all(모든 게시물을 목록에 표시)
   - default_image_url : 대표이미지가 없는 게시물에 설정할 디폴트 이미지 URL
   => 모든 갤러리 목록을 불러오면서 대표 이미지가 없는 게시물은 디폴트 이미지로 설정하는 숏코드
      [mb_board name="board2" list_type="gallery" responsive_class="col-321" gallery_image_type="all" default_image_url="디폴트 이미지 URL" style=""]
* 관리자 > 회원관리 > 회원 정보수정(비밀번호) 기능 개선
* minor bug 수정

= 1.9.3 =
* 워드프레스 5.5버전에서 jQuery Migrate 1.x 삭제로 인해 발생하는 체크박스 선택 관련 버그 수정
* jQuery 3.5.x 호환성 코드 추가 및 수정
* 에디터 컴포저 모바일 반응형 최적화
* minor bug 수정

= 1.9.2 =
* 네이버 유입경로(로그분석) 스크립트 출력 위치 변경
* 캘린더 > 모바일 템플릿 기능 수정
* minor bug 수정

= 1.9.1 =
* 게시판 숏코드를 2번 파싱해서 보여주는 일부 페이지 빌더에서 첨부파일 수정 안되는 버그 수정
* Referer 및 Link URL 템플릿 보안 기능 수정 (URL 주소에 꺽은괄호 및 따옴표가 있을 경우 제거)
* 게시판 삭제시 디비 테이블 삭제 안되는 버그 수정
* 게시판 화면 모드에 따라 태그를 감출 수 있는 CSS 클래스 추가
  => 글목록 화면에서 감추기 클래스(mb-hide-list), 글보기 화면에서 감추기 클래스(mb-hide-view), 글작성 화면에서 감추기 클래스(mb-hide-write)
* 회원 레벨에 따라 태그를 감출 수 있는 CSS 클래스 추가 (mb-hide-level0 ~ mb-hide-level10)
  => 비회원 접속시 감추기 클래스(mb-hide-level0), 레벨1 회원 접속시 감추기 클래스(mb-hide-level1)
* 반응형 빈공간 CSS 클래스 추가 (mb-spacer-10 ~ mb-spacer-100)
* 게시판 정렬 숏코드 속성 추가
   - order_by : 정렬할 게시판 필드 이름
   - order_type : desc(내림차순), asc(오름차순)
   => 게시판을 조회수 순으로 정렬하는 숏코드
     [mb_board name="board1" order_by="fn_hit" order_type="desc" style=""]
   => 게시판을 최근에 수정된 게시물 및 댓글이 달린순으로 정렬하는 숏코드
     [mb_board name="board1" order_by="fn_modify_date" order_type="desc" style=""]

= 1.9.0 =
* 모델링 및 템플릿 확장 기능 수정
* DB > 게시판 좌표 저장 필드(gps_latitude,gps_longitude) 타입 변경: VARCHAR => DECIMAL
  => 업데이트 이후 새로 추가하는 게시판부터 변경된 내용으로 적용
* 게시판 URL 생성시 중복 또는 불필요한 파라미터 제거 기능 수정
* 최적화 및 안정성 개선을 위한 코드 추가
* minor bug 수정

= 1.8.9 =
* 일부 테마와 반응형 클래스 충돌문제로 레이아웃 깨지는 문제 수정
* 모바일 이미지 업로드시 가로/세로 방향을 자동으로 잡아주는 기능 추가
  => 이미지에 EXIF 메타정보가 있을 경우에만 동작
* 이미지 썸네일 생성 기능 최적화
* minor bug 수정

= 1.8.8 =
* 갤러리, 웹진, 문의하기 게시판 CSS 수정
* 검색 엔진 최적화(SEO) 기능 수정 (상품 페이지 SEO 보완)
* 검색 필터 보안 기능 수정
* minor bug 수정

= 1.8.7 =
* HTMLPurifier 버전 업데이트 (4.8.0->4.12.0)
* 게시판 페이지네이션 > 페이지 블럭 위치 자동 조절 기능 추가
* 게시판 모바일 페이지네이션 > 페이지 숫자 표시형태로 수정
* Daum 우편번호 스크립트 파일 - 통합 로딩방식으로 수정 및 레이어 팝업 기능 추가
  => 비즈니스,커머스 패키지: 주소검색 기능이 브라우저 팝업형태에서 주소입력 하단에 레이어가 나타나는 형태로 변경
* 문의하기 게시판 기본 디자인 수정
* 자주묻는질문 게시판 기본 디자인 수정

= 1.8.6 =
* Elementor Page Builder 사용시 게시글 수정화면 깨지는 버그 수정 (망보드 1.8.5버전만 해당)
* minor bug 수정

= 1.8.5 =
* 게시판 설정 옵션값을 게시판 숏코드 속성으로 추가
   [mb_board name="board1" style="" page_size="20" comment_size="50" block_size="10" use_notice="1" use_secret="1" use_comment="1" category_data="카테고리1,카테고리2,카테고리3"]
   - page_size : 게시판 목록 개수 설정
   - comment_size : 댓글 목록 개수 설정
   - block_size : 페이지네이션 블록 개수 설정
   - use_notice : 공지사항 기능 설정 (사용:1, 사용안함: 0)
   - use_list_search : 목록 검색 기능 설정 (사용:1, 사용안함: 0)
   - use_list_title : 목록 제목바 표시 설정 (사용:1, 사용안함: 0)   
   - use_secret : 비밀글 기능 설정 (사용(선택):1, 사용(필수): 2, 사용안함: 0)
   - use_comment : 댓글 기능 설정 (사용:1, 사용안함: 0)
   - category_data : 카테고리 데이타 설정
   => 모바일 및 태블릿에서 속성을 다르게 적용할 경우 "mobile_","tablet_" 접두사를 붙여서 "mobile_page_size","tablet_page_size","mobile_use_comment" 형태로 설정 가능
   => 데스크탑 목록 개수 20개, 태블릿 목록 개수 15개, 모바일 목록 개수 10개로 설정하는 숏코드
     [mb_board name="board1" page_size="20" tablet_page_size="15" mobile_page_size="10"]
* 에디터에 반응형 레이아웃을 쉽게 추가할 수 있는 에디터 컴포저 기능 추가 (Smart Editor, CK Editor 지원)
* 망보드 SEO URL, Canonical, 글보기 소셜공유 URL 주소 수정 (불필요한 파라미터 삭제)

= 1.8.4 =
* 게시판 내용 출력하는 mb_board_item 숏코드 기능 추가
   => board1 게시판의 1번글의 내용을 출력
     [mb_board_item name="board1" pid="1" style=""]
   => 목록,글보기 권한이 0으로 설정된 게시판만 가능
   => pid 값 생략시 가장 최신글의 내용을 출력
* 로그인이 필요한 페이지에 비회원 접속시 페이지 이동없이 로그인 화면 바로 뛰우는 login_page 숏코드 속성 추가
   => 목록보기 권한이 1레벨로 설정된 board2 게시판 숏코드에 login_page="1" 속성을 추가하면 비회원이 접속시 로그인 페이지로 이동없이 바로 로그인 화면 표시
     [mb_board name="board1" style="" login_page="1"]
   => 비즈니스 및 커머스 패키지 페이지 기능에서 사용시 아래 파일을 최신버전 파일로 업데이트 필요
   => 비즈니스 패키지 업데이트 파일 : mangboard/plugins/user/user.php
   => 커머스 패키지 업데이트 파일 : mangboard/plugins/commerce/commerce.php
* 게시물 작성 후 다른 페이지로 이동하는 write_next_url 숏코드 속성 추가
   [mb_board name="board1" style="" write_next_url="/form_result/"]
   => 게시물 등록 후 관리자가 직접 만든 신청완료 페이지로 이동이 필요할 경우 사용
   => 같은 홈페이지 내의 URL 주소만 설정 가능
* 본인이 작성한 글만 보여주는 where, where_level 숏코드 속성 추가
   - where : user_post1(본인이 작성한 게시물만 목록에 표시), user_post2 (본인이 작성한 게시물, 본인 게시물에 작성된 답변만 목록에 표시)
   - where_level : where에 설정한 속성을 특정 레벨 이하인 회원에게만 적용(숫자 7 입력시 레벨 7 이하인 회원들에게만 적용, 생략시 모든 회원에게 적용)
   => 레벨 7이하인 회원이 접속시 본인이 작성한 게시물만 보이게 하는 숏코드
   [mb_board name="board1" style="" where="user_post1" where_level="7"]
* 목록,글보기,글쓰기 항목 감추기 숏코드 속성 추가 (hide_list,hide_view,hide_write)
   - hide_list : 목록 페이지에서 감추고자 하는 항목의 필드명 또는 타입명을 쉼표로 구분해서 입력
   - hide_list_level : hide_list에 설정한 속성을 특정 레벨 이하인 회원에게만 적용 (숫자 7 입력시 레벨 7 이하인 회원들에게만 적용, 생략시 모든 회원에게 적용)
   - hide_list_group : hide_list에 설정한 속성을 특정 그룹 회원에게만 적용 (쉼표로 구분해서 입력가능, 비회원 그룹은 guest, 생략시 모든 회원에게 적용)
   - hide_list_except_group : hide_list에 설정한 속성을 특정 그룹 회원에게만 적용하지 않음 (hide_list에 설정된 항목을 특정 그룹에게만 보이게 할 경우 사용)
   - hide_view : 글보기 페이지에서 감추고자 하는 항목의 필드명 또는 타입명을 쉼표로 구분해서 입력
   - hide_view_level : hide_view에 설정한 속성을 특정 레벨 이하인 회원에게만 적용 (숫자 7 입력시 레벨 7 이하인 회원들에게만 적용, 생략시 모든 회원에게 적용)
   - hide_view_group : hide_view에 설정한 속성을 특정 그룹 회원에게만 적용 (쉼표로 구분해서 입력가능, 비회원 그룹은 guest, 생략시 모든 회원에게 적용)
   - hide_view_except_group : hide_list에 설정한 속성을 특정 그룹 회원에게만 적용하지 않음 (hide_view에 설정된 항목을 특정 그룹에게만 보이게 할 경우 사용)
   - hide_write : 글쓰기 페이지에서 감추고자 하는 항목의 필드명 또는 타입명을 쉼표로 구분해서 입력
   - hide_write_level : hide_write에 설정한 속성을 특정 레벨 이하인 회원에게만 적용 (숫자 7 입력시 레벨 7 이하인 회원들에게만 적용, 생략시 모든 회원에게 적용)
   - hide_write_group : hide_write에 설정한 속성을 특정 그룹 회원에게만 적용 (쉼표로 구분해서 입력가능, 비회원 그룹은 guest, 생략시 모든 회원에게 적용)
   - hide_write_except_group : hide_list에 설정한 속성을 특정 그룹 회원에게만 적용하지 않음 (hide_write에 설정된 항목을 특정 그룹에게만 보이게 할 경우 사용)
   => 목록에서 작성자와 조회수를 감추려면 hide_list="fn_user_name,fn_hit" 형태로 숏코드에 추가 (fn_pid:번호, fn_title:제목, fn_user_name:작성자, fn_reg_date:날짜, fn_hit:조회, fn_content:글내용)
     [mb_board name="board1" hide_list="fn_user_name,fn_hit"]
   => 회원가입 및 회원정보 수정에서 이메일, 휴대폰, 사진, 주소를 감추는 숏코드
     - 회원가입: [mb_user name="user_register" hide_write="fn_user_email,fn_user_phone,fn_user_picture,user_address"]
     - 정보수정: [mb_user name="user_info" hide_write="fn_user_email,fn_user_phone,fn_user_picture,user_address_info"]
   => 글쓰기 페이지에서 레벨 7이하인 회원은 파일업로드 항목을 감추는 숏코드
     [mb_board name="board1" hide_write="file" hide_write_level="7"]
   => 글쓰기 페이지에서 비회원(guest)과 home 그룹에 속하는 회원은 파일업로드 항목을 감추는 숏코드
     [mb_board name="board1" hide_write="file" hide_write_group="guest,home"]
   => 글쓰기 페이지에서 commerce 그룹에 속하는 회원만 파일업로드 항목을 보여주는 숏코드
     [mb_board name="board1" hide_write="file" hide_write_except_group="commerce"]
   => 모바일 및 태블릿에서 속성을 다르게 적용할 경우 "mobile_","tablet_" 접두사를 붙여서 "mobile_hide_write","tablet_hide_write","mobile_hide_write_level" 형태로 설정 가능
* 자료실 스킨 모바일 제목 하단 포맷을 설정할 수 있는 title_format 숏코드 속성 추가
   [mb_board name="board1" style="" title_format="name_date_hit"]
   => 포맷 설정을 통해 순서 변경 및 삭제 가능
   => 날짜,이름,조회수 순으로 순서 변경: "date_name_hit"
   => 조회수 삭제: "name_date", 날짜만 표시: "date"
* 스마트에디터 > URL 입력시 새창연결(target="_blank") 설정기능 추가
* minor bug 수정

= 1.8.3 =
* 갤러리 게시판 기본 숏코드에 반응형 클래스 속성 표시 추가 (responsive_class="col-421")
* 멀티 카테고리 기능 추가
   [mb_board name="board1" style="" category1="패키지,위젯,스킨"]
   => 게시판 숏코드에 카테고리 이름를 쉼표로 구분해서 입력
* 멀티 최근 게시물 기능 추가
   [mb_latest name="board1" title="최근 게시물" join="board2,board3" list_size="5"]
   => 최근게시물 숏코드에 추가로 불러올 게시판 이름을 join 항목에 쉼표로 구분해서 입력 (최대 4개까지 가능)
   => 게시판이 워드프레스 페이지에 숏코드로 등록되어 있는 게시판만 설정 가능
* 모델링 및 템플릿 확장 기능 수정
* 디비 > 날짜 검색 쿼리 최적화 작업
* 테마 최적화 CSS 코드 추가(The7,Enfold)
* minor bug 수정

= 1.8.2 =
* 망보드 이용에 필요한 설치환경이 맞지 않을 경우 에러 메시지 표시 기능 추가
* 네이버,구글,페이스북 Analytics 전환추적(결제,회원가입) 코드 추가 (옵션설정>Analytics ID 입력시에만 작동) 
* 망보드 호환성 향상을 위한 CSS 코드 수정
* minor bug 수정

= 1.8.1 =
* 게시판 목록 좌측 하단에 "게시판 관리" 버튼 추가 (관리자만 표시)
* 파일 다운로드 호환성 기능 수정 (일부환경에서 파일 다운로드 안되는 버그 수정)
* 구글 Analytics 코드 수정 (gtag)

= 1.8.0 =
* 일부 테마환경에서 AJAX 통신 안되는 버그 수정
* 비밀글 수정안되는 버그 수정 (일반 사용자)