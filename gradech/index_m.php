<?php
if( strpos($_SERVER['HTTP_REFERER'], '//skkedu.net') === false )
{
    echo 'error: Access not permitted';
    exit;
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    
    <title>홈페이지 통합관리 패널</title>
    
    <link href="style.css?v=161230_07" rel='stylesheet' type="text/css">
</head>
<body>
    
<style type="text/css">button.btn2 {padding:7px 20px;border:1px solid #444;border-radius:2px;background:#fff;cursor:pointer;}
</style>
<p style="text-align: center;"><strong><span style="font-size:16px;">홈페이지 통합관리 패널 &amp; 가이드 모바일</span></strong></p>

<p>&nbsp;</p>

<p>&clubs; 모바일 페이지에서는 제한적인 기능만 제공합니다.</p>

<p>이외 기능은 PC에서 접속해 주세요.</p>

<p>&nbsp;</p>

<p>① 하단&nbsp;&quot;클럽등급 조정 도구&quot;를 클릭<br />
② 변경하고자 하는 회원의 우측 &#39;수정&#39; 버튼을 클릭<br />
③ 회원 그룹에서 새내기 또는 헌내기를 체크</p>

<p><br />
TIP : 반대로 체크를 해제하면 등급 취소 가능, 아래 &quot;거부&quot; 를 클릭하면 회원 차단 가능</p>

<p>&nbsp;</p>

<p>비회원이 가입해 글을 열람하는 것을 방지하기 위해 수동으로 등급을 변경해주어야 합니다.</p>

<p>&nbsp;</p>

<p>&middot; 전체 새내기 인원 : <a id="newTotal"></a><br />
&middot; 교육학과 :<a id="newEdu"></a><br />
&middot; 수학교육과 :<a id="newMath"></a><br />
&middot; 컴퓨터교육과 :<a id="newComputer"></a><br />
&middot; 한문교육과 : <a id="newHan"></a></p>

<p>&nbsp;</p>

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
                        
<p style="text-align: center;"><button class="btn2" onclick="window.location.href=https://skkedu.net/index.php?act=dispVicemanagerMemberList&quot;">클럽등급 조정 도구</button></p>
    
    <script src="main.php" type="text/javascript"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js"></script>
    <script type="text/javascript">
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
</script>
    
</body>
</html>