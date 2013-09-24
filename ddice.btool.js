$(document).ready(function(){
(function($) {
    Array.prototype.empty = function() {
        this.length = 0;
        return this;
    };
    if (typeof String.prototype.trim == 'undefined') {
        String.prototype.trim = function() {
            return this.replace(/(^\s*)|(\s*$)/gi, "");
        }
    }
    
    function callUrl(htParam) {
        var sBase = 'http://kcopper8.dothome.co.kr/ddice/ddice.php';
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
                alert(config.nMaxDiceSelect + "������ ���� �� �ֽ��ϴ�.");
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
                alert("���̽��� �������ּ���!");
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
                    break;
            }
        });
        
        
        return {
            'show' : function(target, id) {
                
                _clear();
                $elDDiceForm.show();
                htOffset = $(target).offset();
                $elDDiceForm.offset({
                    top : htOffset.top + 10
                    , left : htOffset.left + 10
                });
                targetId = id;
            }
        };
    };
    
   
    $.fn.ddice = function(settings) {
        var $this = $(this);
        var config = {
            tplRoll : '<button class="ddice-button" style="font-size:8pt; margin: 0; padding: 0;">�ֻ��� ������</button>'
            , tplResult : '<div class="ddice_result"><ul>#result#</ul></div>'
            , tplResultDice : '<li><img src="http://kcopper8.dothome.co.kr/ddice/dice/dice#type#/#type#dice#value#.png" alt="#type#��ü ��� #value#" /></li>'
            , sCss : ''
        };
                
        if (settings) $.extend(config, settings);
        
        var aDdiceIds = [];
        this.each(function() {
            aDdiceIds.push($(this).attr("ddice-id"));
        });


        var htFnDdiceGenerate = {
            "unregistered" : function($target, vDData) {
                // do nothing
            },
            "registered" : function($target, vDData) {
                // do nothing
            },
            "rolled" : function($target, vDData) {
                var sHtml = "";
                $.each(vDData.dices, function(nIdx, nVal) {
                    var sDiceHtml = config.tplResultDice.replace(/#type#/g, nVal.type);
                    sDiceHtml = sDiceHtml.replace(/#value#/g, nVal.value);
                    
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
        
        {

        var ss1 = document.createElement('style');
        var def = config.sCss;
        ss1.setAttribute("type", "text/css");
        var hh1 = document.getElementsByTagName('head')[0];
        hh1.appendChild(ss1);
        if (ss1.styleSheet) {   // IE
            ss1.styleSheet.cssText = def;
        } else {                // the world
            var tt1 = document.createTextNode(def);
            ss1.appendChild(tt1);
        }
           //$('<STYLE type="text/css"></STYLE>').appendTo("HEAD").html(config.sCss);
        }

        $('<input type="checkbox" name="ddice"> <span>�ֻ���</span>').appendTo($("FORM[name=write2] TABLE").find("TR:last TD:first"));
        js_input_checkboxs_skin($('INPUT[name=ddice]'));

        $('INPUT[name=ddice]').click(function() {
            oDDiceForm.show(this, '');
        });

        return this;
    };

    var sFrmHtml = ''
    + '<div class="ddice_form" style="display:none;">\n'
+ '    <div class="ddice_header">\n'
+ '        <h3>���̽� ������</h3>\n'
+ '        <a href="#" title="�ݱ�" class="ddice_close_btn" ddice-click="close">X</a>\n'
+ '    </div>\n'
+ '    <dl>\n'
+ '        <dt>���̽� ����</dt>\n'
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
+ '        <dt>������ ���̽�</dt>\n'
+ '        <dd>\n'
+ '    <ul class="ddice_dices ddice_output_dices">\n'
+ '        <li><button>10</button></li>\n'
+ '    </ul>\n'
+ '        </dd>\n'
+ '    </dl>\n'
+ '    <p class="ddice_desc remove">������ ���̽��� Ŭ���ϸ� ������ �� �ֽ��ϴ�.</p>\n'
+ '    <div class="ddice_roll"><button class="ddice_roll_btn" ddice-click="roll">������</button></div>\n'
+ '</div>\n'

    

    var oDDiceForm = DDiceForm({
        'sFrmHtml' : sFrmHtml
    });
}(jQuery));

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
    $("[ddice-id]").ddice({
        "sCss" : sCss
    });

});