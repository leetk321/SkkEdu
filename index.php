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
    
    <link href="style.css" rel='stylesheet' type="text/css">
</head>
<body>
    <div class="container">
        <header class="wrapper-open">
            <div class="wrapper">
                <h1 class="column dt t5 m0">홈페이지 관리 패널</h1>
                <p class="floatFix"></p>
            </div>
        </header>
        <div class="wrapper content">
            <div class="item column d5 t0 m0">
                <p class="box"><strong><font size = 4>★ 사용방법</font></strong> <button class="btn2" onclick=window.location.href="http://skkedu.net/index.php?act=dispVicemanagerMemberList">개별등급 조정 도구</button></br></br>
                - 학생회 소개 : 학생회 소개 추가 가이드</br>
                - 등급 조정 : 회장단, 집행부 및 새내기 클럽 등급 조정 가이드</br>
                - 게시물 관리 : 게시물 관리 가이드 (글 이동 및 삭제, 공지 등록 및 삭제)</br>
                - 관리도구 사용 : 하단 관리도구 사용 가이드</br>
                </p>
                <p class = "box"><font color = "red">※ 주의 : 하단 관리도구 사용 전 반드시 관리도구 사용 가이드를 숙지해주세요.</font></p>
            </div>
            <div class="item column d5 t0 m0">
                <div class="title">게시물 아카이브</div>
                <ul>
                    <li>
                        모두 아카이브로 이동
                        <button class="btn" onClick="javascript:archive('all')">이동</button>
                    </li>
                    <li>
                        교육학과 게시물만 이동
                        <button class="btn" onClick="javascript:archive('edu')">이동</button>
                    </li>
                    <li>
                        수학교육과 게시물만 이동
                        <button class="btn" onClick="javascript:archive('math')">이동</button>
                    </li>
                    <li>
                        컴퓨터교육과 게시물만 이동
                        <button class="btn" onClick="javascript:archive('computer')">이동</button>
                    </li>
                    <li>
                        한문교육과 게시물만 이동
                        <button class="btn" onClick="javascript:archive('han')">이동</button>
                    </li>
                </ul>
            </div>
            <p class="floatFix"></p>
            <div class="item column d5 t0 m0">
                <div class="title">새내기를 헌내기로 일괄변경</div>
                <ul>
                    <li>
                        전체 새내기 인원 : <a id="newTotal"></a>
                        <button class="btn" onClick="javascript:deleteGrade('newbie')">등급 일괄취소</button>
                        <button class="btn" onClick="javascript:changeGrade('newbie')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        교육학과 : <a id="newEdu"></a>
                        <button class="btn" onClick="javascript:changeGrade('edu')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        슈학교육과 : <a id="newMath"></a>
                        <button class="btn" onClick="javascript:changeGrade('math')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        컴퓨터교육과 : <a id="newComputer"></a>
                        <button class="btn" onClick="javascript:changeGrade('computer')">헌내기로 일괄변경</button>
                    </li>
                    <li>
                        한문교육과 : <a id="newHan"></a>
                        <button class="btn" onClick="javascript:changeGrade('han')">헌내기로 일괄변경</button>
                    </li>
                    <li class="highlight">
                        연산시간 : <a id="ms"></a>
                        <button class="btn" onClick="javascript:checkNewbie()">헌내기로 일괄변경</button>
                    </li>
                </ul>
            </div>
            <div class="item column d5 t0 m0">
                <div class="title">새내기 등급 취소</div>
                <ul>
                    <li>
                        모든 새내기 등급취소
                        <button class="btn" onClick="javascript:deleteGrade('newbie')">등급취소</button>
                    </li>
                    <li>
                        교육학과만 
                        <button class="btn" onClick="javascript:deleteGrade('edu')">등급취소</button>
                    </li>
                    <li>
                        수학교육과만
                        <button class="btn" onClick="javascript:deleteGrade('math')">등급취소</button>
                    </li>
                    <li>
                        컴퓨터교육과만
                        <button class="btn" onClick="javascript:deleteGrade('computer')">등급취소</button>
                    </li>
                    <li>
                        한문교육과만
                        <button class="btn" onClick="javascript:deleteGrade('han')">등급취소</button>
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
            var prompt = window.prompt( '해당 카테고리의 새내기를 모두 헌내기로 변경합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
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
            var prompt = window.prompt( '해당 카테고리의 새내기의 등급을 일괄 취소합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
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
            var prompt = window.prompt( '해당 카테고리의 새내기를 모두 헌내기로 변경합니다. 명령을 실행하게 되면 다시 원래대로 복구하기 힘듭니다. 아래의 콘솔에 "이해했습니다"를 입력하여 다음단계로 진행하여 주십시오.' );
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