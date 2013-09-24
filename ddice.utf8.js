$(document).ready(function(){
(function($) {
    Array.prototype.empty = function() {
        this.length = 0;
        return this;
    };
    
    function callUrl(htParam) {
        var sBase = 'http://local.hunter.net/ddice/ddice.php';
        htParam['ddiceKey'] = 'kayzero';
        
        var aParam = [];
        $.each(htParam, function(key, val) {
            if (key) {
                aParam.push(key + '=' + (val ? val : ''));
            }
        });
        
        return sBase + '?' + aParam.join('&');
    }
    
    var DDiceForm = function(settings) {
        var config = {
            selected_dice : '<li><button ddice-click="remove">#vVal#</button></li>'
            , nMaxDiceSelect : 20
            , sFrmHtml : ''
        };
        
        if (settings) $.extend(config, settings);
        
        var $frm = $elDDiceForm = $(config.sFrmHtml).appendTo("BODY");
        $frm.output = $frm.find(".ddice_output_dices").first();
        var aDices = [];
        var targetId = "";
        
        function _draw() {
            $frm.output.empty();
            $.each(aDices, function(nIdx, vVal) {
                $(config.selected_dice.replace('#vVal#', vVal)).appendTo($frm.output);
            });
        }
        
        function _add(nDiceType) {
            if (aDices.length >= config.nMaxDiceSelect) {
                alert(config.nMaxDiceSelect + "개까지 굴릴 수 있습니다.");
                return;
            }
            aDices.push(nDiceType);
            _draw();
        }
        
        function _remove(nIndex) {
            aDices.splice(nIndex, 1);
            _draw();
        }
        
        function _clear() {
            aDices.empty();
            _draw();
            
        }
        
        function _roll() {
            if (aDices.length < 1) {
                alert("다이스를 선택해주세요!");
                return;
            }
            
            $.ajax(callUrl({
                'action' : 'roll'
                , 'id' : targetId
                , 'types' : aDices.join(",")
            }), {
                "dataType" : "jsonp",
                "success" : function(oData) {
                    location.reload();
                }
            });
            $frm.hide();
            _clear();
        }
        
        $elDDiceForm.click(function(oEvent) {
            switch($(oEvent.target).attr('ddice-click')) {
                case "select":
                    _add($(oEvent.target).html().trim());
                    break;
                case "remove":
                    var nIndex = $(oEvent.target).parent().children("BUTTON").index(oEvent.target);
                    _remove(nIndex);
                    break;
                case "close":
                    $elDDiceForm.hide();
                    break;
                case "roll":
                    _roll();
                    break;
                default:
                    console.log("click, but no handler for " + $(oEvent.target).attr('ddice-click'));
                    console.log(oEvent.target);
                    break;
            }
        });
        
        return {
            "show" : function(target, id) {
                _clear();
                $elDDiceForm.show();
                htOffset = $(target).offset();
                $elDDiceForm.offset({
                    top : htOffset.top + 10
                    , left : htOffset.left + 10
                });
                targetId = id;
            },

        };
    };
    
   
    $.fn.ddice = function(settings) {
        var $this = $(this);
        var config = {
            tplRoll : '<button class="ddice-button">roll</button>'
            , tplResult : '<div class="ddice_result"><ul>#result#</ul></div>'
            , tplResultDice : '<li><img src="http://local.hunter.net/ddice/#type#-#value#.gif" alt="#type#면체 결과 #value#" /></li>'
            , sCss : ''
        };
                
        if (settings) $.extend(config, settings);
        
        var aDdiceIds = [];
        this.each(function() {
            aDdiceIds.push($(this).attr("ddice-id"));
        });


        var htFnDdiceGenerate = {
            "unregistered" : function($target, vDData) {
                $target.html(config.tplRoll);
                $target.click(function() {
                    oDDiceForm.show(this, $target.attr("ddice-id"));
                })
            },
            "registered" : function($target, vDData) {
                // do nothing
            },
            "rolled" : function($target, vDData) {
                var sHtml = "";
                $.each(vDData.dices, function(nIdx, nVal) {
                    var sDiceHtml = config.tplResultDice.replace(/#type#/, nVal.type);
                    sDiceHtml = sDiceHtml.replace(/#value#/, nVal.value);
                    
                    sHtml += sDiceHtml;
                });
                $target.html(config.tplResult.replace('#result#', sHtml));
            }
        };
           
        $.ajax(callUrl({
            'action' : 'get'
            , 'ids' : aDdiceIds.join(",")
        }), {
            "dataType" : "jsonp",
            "success" : function(oData) {
                $this.each(function() {
                    var $ddiceDiv = $(this);
                    var vDdiceData = oData[$ddiceDiv.attr("ddice-id")];
                
                    var sState = "";
                    if (!vDdiceData) {
                        sState = "unregistered";
                    } else {
                        sState = vDdiceData.state;
                    }
                    htFnDdiceGenerate[sState]($(this), vDdiceData);
                });
            }
        });
        
        $('<STYLE type="text/css"></STYLE>').appendTo("HEAD").html(config.sCss);
        return this;
    };
    
    var sCss = ""
+ ".ddice_form {display:block;position:absolute;}\n"
+ ".ddice_form UL,LI,DL,DD,DT,DIV,BUTTON,H3 {margin: 0 0 0 0;padding:0 0 0 0;}\n"
+ ".ddice_form {border:1px solid #c0c0c0; box-shadow: inset 0 1px 2px #e4e4e4;width:330px;font-size:9pt;background: #FFFFFF;}\n"
+ ".ddice_form .ddice_dices LI {display:inline;}\n"
+ ".ddice_form .ddice_dices BUTTON {width:30px; font-size:8pt;vertical-align:middle;height:30px;}\n"
+ ".ddice_form DL DT {float:left;width:80px;margin: 0 10px 0 10px;padding: 5px 0 5px 0;}\n"
+ ".ddice_form DL DD {width:220px;margin:0 0 0 90px;padding: 5px 0 5px 10px;min-height:30px;}\n"
+ ".ddice_form .ddice_header {padding: 5px;font-size:8pt;}\n"
+ ".ddice_form .ddice_header H3 {display:inline-block;}\n"
+ ".ddice_form .ddice_header A {text-decoration:none;display:inline-block;text-align:right;float:right;}\n"
+ ".ddice_form .ddice_output_dices BUTTON:hover {cursor:crosshair;}\n"
+ ".ddice_form .ddice_roll {text-align: right;padding-right:10px;padding-bottom:10px;}\n"
+ ".ddice_form .ddice_roll_btn {width:70px;}\n"
+ ".ddice_form .ddice_desc {margin : 10px 10px 3px 10px;color:#8d8d8d;}\n"
+ "\n"
+ ".ddice_result LI {display:inline;}\n"
+ ".ddice_result UL {margin: 0 0 0 0;padding: 0 0 0 0;}\n"
;

    var sFrmHtml = ''
    + '<div class="ddice_form" style="display:none;">\n'
+ '    <div class="ddice_header">\n'
+ '        <h3>다이스 굴리기</h3>\n'
+ '        <a href="#" title="닫기" class="ddice_close_btn" ddice-click="close">X</a>\n'
+ '    </div>\n'
+ '    <dl>\n'
+ '        <dt>다이스 종류</dt>\n'
+ '        <dd>\n'
+ '            <ul class="ddice_dices">\n'
+ '        <li><button disabled>4</button></li>\n'
+ '        <li><button disabled>6</button></li>\n'
+ '        <li><button disabled>8</button></li>\n'
+ '        <li><button ddice-click="select">10</button></li>\n'
+ '        <li><button disabled>12</button></li>\n'
+ '        <li><button disabled>100</button></li>\n'
+ '            </ul>\n'
+ '        </dd>\n'
+ '        <dt>선택한 다이스</dt>\n'
+ '        <dd>\n'
+ '    <ul class="ddice_dices ddice_output_dices">\n'
+ '        <li><button>10</button></li>\n'
+ '    </ul>\n'
+ '        </dd>\n'
+ '    </dl>\n'
+ '    <p class="ddice_desc remove">선택한 다이스를 클릭하면 제거할 수 있습니다.</p>\n'
+ '    <div class="ddice_roll"><button class="ddice_roll_btn" ddice-click="roll">굴리기</button></div>\n'
+ '</div>\n'

    

    var oDDiceForm = DDiceForm({
        'sFrmHtml' : sFrmHtml
    });


$("[ddice-id]").ddice({
    "sCss" : sCss
});
    
}(jQuery));
});