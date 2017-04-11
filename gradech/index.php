<?php
if( strpos($_SERVER['HTTP_REFERER'], '//skkedu.net') === false )
{
    echo 'error: Access not permitted';
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    
    <title>새내기클럽 관리</title>
    
    <link href="style.css?v=170220_02" rel='stylesheet' type="text/css">
</head>
<body>
    <div class="container">
        <header class="wrapper-open">
            <div class="wrapper">
                <h1 class="column dt t5 m0">홈페이지 통합관리 패널 & 가이드</h1>
                <p class="floatFix"></p>
            </div>
        </header>
        <div class="wrapper content">
            <div class="item column d5 t0 m0">
                <p class="box"><strong><font size = 4>★ 새내기 클럽 관리방법</font></strong> <button class="btn2" onclick=window.location.href="https://skkedu.net/index.php?act=dispVicemanagerMemberList">클럽등급 조정 도구</button></br></br>
            </br>
                1. 새내기·헌내기 등급 조정</br>
                비회원이 가입해 글을 열람하는 것을 방지하기 위해 수동으로 등급을 변경해주어야 합니다.</br>
                ① 우측 "클럽등급 조정 도구"를 클릭<br/>
                ② 변경하고자 하는 회원의 우측 '수정' 버튼을 클릭</br>
                ③ 회원 그룹에서 새내기 또는 헌내기를 체크</br>
                TIP : 반대로 체크를 해제하면 등급 취소 가능, 아래 "거부" 를 클릭하면 회원 차단 가능</br></br>
                2. 게시물 관리 : 게시물 관리 가이드 참고 (글 이동 및 삭제, 공지 등록 및 삭제)</br></br>
                <button class="btn3" onclick="window.open('about:blank')">게시물 관리 가이드 (새 창) : 준비중</button><br/>  
                3. 클럽 관리자 ID / Password 재설정 (재설정은 최고 관리자만 가능합니다)</br>
                ① 교과 : eduadmin <a href = "https://skkedu.net/index.php?module=admin&act=dispMemberAdminInsert&member_srl=5568">재설정</a> 
                ② 수교 : mathadmin <a href = "https://skkedu.net/index.php?module=admin&act=dispMemberAdminInsert&member_srl=5569">재설정</a> 
                ③ 컴교 : comadmin <a href = "https://skkedu.net/index.php?module=admin&act=dispMemberAdminInsert&member_srl=5570">재설정</a> 
                ④ 한교 : hanadmin <a href = "https://skkedu.net/index.php?module=admin&act=dispMemberAdminInsert&member_srl=5571">재설정</a><br/>
                <strong>※ 닉네임, 이름, 이메일 변경 등 계정의 개인화는 차기 학생회 운영을 위해 자제하여 주시기 바랍니다.</strong><br/>
                <strong>※ 비밀번호는 꼭 변경하여 과별로 별도 관리하고 문제가 생길 시 최고 관리자 계정(skkedu)을 이용, 재설정하여 주시기 바랍니다.</strong><br/><br/>
                4. 매년 수시 합격 발표 전, 학교에 공문으로 합격자발표 팝업에 사범대 새내기클럽 주소 추가를 요청해야 합니다.<br/>
                (주소 : http://club.skkedu.net)<br/>
                또한 클럽 사용을 시작하기 전(매년 12월 초), 초기 설정을 위해 하단의 관리도구를 사용하여야 합니다.</br>
                </p>
                <p class="box"><strong><font size = 4>★ 학생회 홈페이지 관리방법</font></strong><br/><br/>
                    
                1. 전체등급 관리 도구 (최고관리자 및 skkedu 계정만 이용 가능)<br/>
                회장단, 집행국장, 집행부, 최고관리권한 부여 등 모든 권한 설정이 가능합니다.<br/>
                                    <button class="btn41" onclick="window.open('about:blank')">권한 설정 가이드 (새 창) : 준비중</button>
                <button class="btn42" onclick="window.open('https://skkedu.net/index.php?module=admin&act=dispMemberAdminList')">전체등급 관리 도구 (새 창)</button><br/>     
                2. 호스팅 및 도메인 연장<br/>
                    
                홈페이지 유지비용은 매년 해당 시기에 처리되어야 합니다.<br/>
                처리가 지연될 경우 홈페이지 접속이 불가하거나 지연에 따른 추가비용을 지불해야 할 수 있습니다.<br/>
                마감일 10일~3일 이전에 처리하는 것이 바람직합니다.<br/>
                <strong>※ 도메인 만료 : 매년 5월 1일, 호스팅 만료 : 매년 8월 11일</strong><br/>
                <button class="btn5" onclick="window.open('https://skkedu.net/guide_cost')">유지비용 처리 가이드 (새 창)</button><br/>    
                3. 게시물 관리 : 게시물 관리 가이드 참고 (글 이동 및 삭제, 공지 등록 및 삭제)<br/><br/>
                <button class="btn6" onclick="window.open('about:blank')">게시물 관리 가이드 (새 창) : 준비중</button><br/>               
                4. 비밀번호 분실 및 기타 기술 지원 문의<br/>
                    
                우측 버튼을 클릭, 아래 문제에 대해 홈페이지 기술지원 담당자에게 문의를 보낼 수 있습니다.<br/><br/>
                    
                - 홈페이지 관리방법 문의<br/>
                - 홈페이지 운영상의 문제 발생<br/>
                - 최고 관리자(skkedu) 비밀번호 분실<br/>
                - 홈페이지 이용 중 기술적인 문제 발생<br/>
                - 버그 및 이상동작 발견 제보<br/>
                - 기능 추가 제안<br/>
                <button class="btn7" onclick="window.open('https://skkedu.net/fix')">기술지원 도구 (새 창)</button><br/>
 
                <p class="box"><strong><font size = 4>★ 그룹·계정별 권한 가이드</font></strong><br/><br/>

1) 각 과 클럽 접근 및 이용권한<br/>

- '교육학과', '수학교육과', '컴퓨터교육과', '한문교육과' 그룹 : 각 과 클럽 접근권한, 새내기/헌내기 소개 게시판 이용<br/>

- '새내기', '헌내기' 그룹 : 본인 학과 새내기 클럽 내 모든 게시판 열람<br/>

- edu, math, com, hanadmin 계정 : 각 과 새내기클럽 관리 권한, '클럽등급(새내기/헌내기) 조정 도구' 이용 가능<br/><br/>

2) 사범대학생회 홈페이지<br/>

* 우리사이, 사진첩은 자유롭게 이용이 가능하며 나머지 게시판은 집행부 이상으로 글 작성 제한<br/>

- 집행부 : 사범대 소식 / 회칙·회의·서식 / 집행부·실무 게시판 글 작성<br/>
- 집행국장 : 집행부 권한 + 홈페이지 내 모든 게시판 관리권한<br/>
- 회장단 : 집행부 권한 + 홈페이지 내 모든 게시판 관리권한<br/><br/>

3) [홈페이지 관리패널] 접근권한<br/>

- '회장단', '관리자(edu, math, com, hanadmin)' 그룹 접근 가능<br/><br/>

4) 최고 관리 권한<br/>

- 모든 권한 부여 (새내기 클럽 + 학생회 홈페이지)<br/>
- '전체등급 조정 도구' 이용 가능<br/><br/>

※ 유의사항<br/>

1. 새내기클럽 관리 권한은 위 4개 계정에 종속되어 있으므로 해당 계정을 이용하여야 합니다.<br/>

2. '회장단' 권한으로도 새내기 클럽 접근은 불가능합니다. <br/>
                    새내기 클럽 + 학생회 홈페이지를 자유롭게 접근·관리하는 권한은 최고 관리 권한을 부여받은 계정에 한합니다.


</p>
 <p class = "box"><font color = "red"><strong>※ 주의 : 하단 관리도구 사용 전 반드시 적혀 있는 가이드를 숙지해주세요.</br>
                    매년 12월 초 클럽 사용 전 초기 설정을 위한 기능이므로, 클럽 사용 중 아래 기능은 절대 사용하지 마세요.</strong></font></p>
            </div>
            <div class="item column d5 t0 m0">
                <div class="title">게시물 아카이브</div>
                <ul>
                    <li>
                        모두 아카이브로 이동
                        <button class="btn3" onClick="javascript:archive('all')">이동</button>
                    </li>
                    <li>
                        교육학과 게시물만 이동
                        <button class="btn3" onClick="javascript:archive('edu')">이동</button>
                    </li>
                    <li>
                        수학교육과 게시물만 이동
                        <button class="btn3" onClick="javascript:archive('math')">이동</button>
                    </li>
                    <li>
                        컴퓨터교육과 게시물만 이동
                        <button class="btn3" onClick="javascript:archive('computer')">이동</button>
                    </li>
                    <li>
                        한문교육과 게시물만 이동
                        <button class="btn3" onClick="javascript:archive('han')">이동</button>
                    </li>
                    <li>
                        * Guide : 새내기 클럽 재사용을 위해 전체 게시물을 '이전 게시판'으로 이동하는 기능입니다.<br/>
                        - 이동 대상 : 공지사항, 자유게시판, 익명게시판, 새내기 소개, 헌내기 소개</br>
                        - 제외 대상 : 소모임·학회, 시간표(새내기들에게 정보 제공을 위해 이동 대상에서 제외), 사진첩 <br/>
                        ※ 기능 실행 후 취소가 불가능하므로, 신중하게 실행해 주세요.</br>
                    </li>
                </ul>
            </div>
            <p class="floatFix"></p>
            <div class="item column d5 t0 m0">
                <div class="title">새내기 등급 일괄변경</div>
                <ul>
                    <li>
                        전체 새내기 인원 : <a id="newTotal"></a>
                        <button class="btn2" onClick="javascript:deleteGrade('newbie')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('newbie')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        교육학과 : <a id="newEdu"></a>
                        <button class="btn2" onClick="javascript:deleteGrade('edu')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('edu')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        수학교육과 : <a id="newMath"></a>
                        <button class="btn2" onClick="javascript:deleteGrade('math')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('math')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        컴퓨터교육과 : <a id="newComputer"></a>
                        <button class="btn2" onClick="javascript:deleteGrade('computer')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('computer')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        한문교육과 : <a id="newHan"></a>
                        <button class="btn2" onClick="javascript:deleteGrade('han')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('han')">헌내기로 일괄변경</button>
                    </li>
                    <li class="highlight">
                        연산시간 : <a id="ms"></a>
                        <button class="btn4" onClick="javascript:checkNewbie()">새로고침</button>
                    </li>
                    <li>
                        * Guide : 새내기 클럽 재사용을 위해 새내기 등급을 일괄 헌내기로 변경하거나 새내기 지정을 취소하는 기능입니다.<br/>
                        - 헌내기로 일괄변경(새내기->헌내기) 또는 등급 일괄취소(새내기 지정취소) 둘 중 하나를 선택해 실행해 주세요.<br/>
                        ※ 기능 실행 후 취소가 불가능하므로, 신중하게 실행해 주세요.<br/>
                        ※ 동작으로 변경된 등급은 해당 회원의 로그인과 동시에 반영되므로, '개별등급 조정 도구'에서는 변경 전 등급으로 보일 수도 있습니다.<br/>
                    </li>
                </ul>
            </div>
            <p class="floatFix"></p>
        </div>
    </div>
    
    <script src="main.php" type="text/javascript"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js"></script>
    <script type="text/javascript">

        window.onload = function()
        {
            // 버튼 레이아웃 최적화
            var buttons = document.querySelectorAll('.btn');
            for( var i=0; i<buttons.length; i++ )
            {
                buttons[i].style.marginTop = buttons[i].offsetHeight / 2 * -1 + 'px';
            }
            
            checkNewbie();
        }
        
        function changeGrade( department )
        {
            // 확인메시지 출력
            var prompt = window.prompt( '경고 : 해당 카테고리의 새내기를 모두 헌내기로 변경합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
            if( prompt === '이해했습니다' || prompt === '이해했습니다.' )
            {
                var confirm = window.confirm( '명령을 취소할 수 있는 마지막 기회입니다.' );
                if( confirm )
                {
                    window.alert( '명령을 실행합니다.' );
                    
                    // 인스턴스 생성
                    var s = new SKK();

                    if( s.changeGrade( department ) )
                    {
                        checkNewbie();
                    } 
                    else 
                    {
                        window.alert('내부 오류 발생. 다시 시도하여 주십시오.'); 
                    }
                }
            }
            else if( prompt === null )
            {
            }
            else 
            {
                window.alert('잘못 입력하셨습니다.'); 
            }
        }
        
        function deleteGrade( department )
        {
            // 확인메시지 출력
            var prompt = window.prompt( '경고 : 해당 카테고리의 새내기의 등급을 일괄 취소합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
            if( prompt === '이해했습니다' || prompt === '이해했습니다.' )
            {
                var confirm = window.confirm( '명령을 취소할 수 있는 마지막 기회입니다.' );
                if( confirm )
                {
                    window.alert( '명령을 실행합니다.' );
                    
                    // 인스턴스 생성
                    var s = new SKK();

                    if( s.deleteGrade( department ) )
                    {
                        checkNewbie();
                    } 
                    else 
                    {
                        window.alert('내부 오류 발생. 다시 시도하여 주십시오.'); 
                    }
                }
            }
            else if( prompt === null )
            {
            }
            else 
            {
                window.alert('잘못 입력하셨습니다.'); 
            }
        }
        
        function checkNewbie()
        {
            // 메인 인스턴스 생성
            var s = new SKK();
            
            // 신입생 현황 체크
            var newbie = s.getMember(),
                departmentElem = ['newEdu', 'newMath', 'newComputer', 'newHan'],
                numberOfAllNewbie;
            
            if( newbie === 0 )
            {
                numberOfAllNewbie = 0;
            }
            else
            {
                numberOfAllNewbie = newbie.length;
            }
            
            document.getElementById('newTotal').innerHTML = '<img src="./loader.gif" alt="loading">';
            for( var i=0; i<departmentElem.length; i++ )
            {
                document.getElementById( departmentElem[i] ).innerHTML = '<img src="./loader.gif" alt="loading">';
            }
            
            window.setTimeout(function()
            {
                document.getElementById('newTotal').innerHTML = numberOfAllNewbie + '명';

                var a = new Date().getTime();
                
                if( newbie.length > 0 )
                {
                    // 과별 신입생 현황 체크
                    var department = ['edu', 'math', 'computer', 'han'];

                    for( var i=0; i<department.length; i++ )
                    {
                        var numberOfnewbie = 0,
                            memberOfdepartment = s.getMember( department[i] );
//                        for( var j=0; j<memberOfdepartment.length; j++ )
//                        {
//                            for( var k=0; k<newbie.length; k++ )
//                            {
//                                if( memberOfdepartment[j] === newbie[k] )
//                                {
//                                    numberOfnewbie++;
//                                }
//                            }
//                        }
                        for( var j=0; j<newbie.length; j++ )
                        {
                            for( var k=0; k<memberOfdepartment.length; k++ )
                            {
                                if( memberOfdepartment[k] === newbie[j] )
                                {
                                    numberOfnewbie++;
                                    break;
                                }
                            }
                        }
                        document.getElementById( departmentElem[i] ).innerHTML = numberOfnewbie + '명';
                    }
                }
                else
                {
                    for( var i=0; i<departmentElem.length; i++ )
                    {
                        document.getElementById( departmentElem[i] ).innerHTML = '0명';
                    }
                }
                var b = new Date().getTime();
                document.getElementById( 'ms' ).innerHTML = b - a + 'ms';
            }, 500);
        }
        
        function archive( bid )
        {
            // 확인메시지 출력
            var prompt = window.prompt( '경고 : 기존 게시물을 "이전 게시판"으로 이전합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
            if( prompt === '이해했습니다' || prompt === '이해했습니다.' )
            {
                var confirm = window.confirm( '명령을 취소할 수 있는 마지막 기회입니다.' );
                if( confirm )
                {                 
                    var prompt
                    $s = new SKK();
                    $s.changeBoard( bid );
                    window.alert( '게시물 아카이브가 완료되었습니다.' );   
                }
            }
            else if( prompt === null )
            {
            }
            else 
            {
                window.alert('잘못 입력하셨습니다.'); 
            }
        }
    </script>
</body>
</html>