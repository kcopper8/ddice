$(document).ready(function(){
(function($) {
    // require fix for use
    // like "http://ddice.domain.org/ddice/"
    var serviceUrlPrefix = "<%=serviceUrlPrefix %>";
    // require fix for use

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
        var sBase = serviceUrlPrefix + 'ddice.php';
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
        var fnComplete = function() {};
        
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
            
            fnComplete(aDices.join(","));

            $frm.hide();
            _clear();
        }

        function _open(target, sVal) {
            _clear();
            $elDDiceForm.show();
            var htOffset = $(target).offset();
            $elDDiceForm.offset({
                top : htOffset.top + 10
                , left : htOffset.left + 10
            });

            if (sVal) {
                sVal = sVal.trim();
                if (/^((4|6|8|10|12|20),)*(4|6|8|10|12|20)$/.test(sVal)) {
                    var aNewDices = sVal.split(',');
                    aDices = $.map(aNewDices, function(sDiceType) {
                        return parseInt(sDiceType);
                    });

                    _draw();
                }
            }
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
                    oEvent.preventDefault();
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
                _open(target);

                fnComplete = function(sValue) {
                    $.ajax(callUrl({
                        'action' : 'roll'
                        , 'id' : id
                        , 'types' : sValue
                    }), {
                        "dataType" : "jsonp",
                        "success" : function(oData) {
                            location.reload();
                        }
                    });
                };
            },
            'attachInput' : function(target) {
                var sVal = $(target).find("INPUT[name=ddice]").val();
                _open(target, sVal);
                console.log(target);

                fnComplete = function(sValue) {
                    console.log(sValue);
                    console.log(target);
                    $(target).find("INPUT[name=ddice]").val(sValue);
                };
            }
        };
    };
    
   
    $.fn.ddice = function(settings) {
        var $this = $(this);
        var config = {
            tplRoll : '<button class="ddice-button" style="font-size:8pt; margin: 0; padding: 0;">�ֻ��� ������</button>'
            , tplResult : '<div class="ddice_result"><ul>#result#</ul></div>'
            , tplResultDice : '<li><img src="'+ serviceUrlPrefix + 'dice/dice#type#/#type#dice#value#.png" alt="#type#��ü ��� #value#" /></li>'
            , sCss : ''
        };
                
        if (settings) $.extend(config, settings);
        
        var aDdiceIds = [];
        this.each(function() {
            if ($(this).attr("ddice-id")) {
                aDdiceIds.push($(this).attr("ddice-id"));
            }

            if ($(this).attr("ddice") == 'form') {
                var elDDiceFormType = this;
                $('<a href="#"><img src="' + serviceUrlPrefix + 'dice/dice2.png"></a><input type="hidden" name="ddice">')
                    .appendTo(this)
                    .click(function(oEvent) {
                        oEvent.preventDefault();
                       oDDiceForm.attachInput(elDDiceFormType);
                    });
            }
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
+ '        <li><button ddice-click="select">4</button></li>\n'
+ '        <li><button disabled>6</button></li>\n'
+ '        <li><button ddice-click="select">8</button></li>\n'
+ '        <li><button ddice-click="select">10</button></li>\n'
+ '        <li><button ddice-click="select">12</button></li>\n'
+ '        <li><button ddice-click="select">20</button></li>\n'
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
+ '    <div class="ddice_roll"><button class="ddice_roll_btn" ddice-click="roll">���ÿϷ�</button></div>\n'
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
    $("[ddice]").ddice({
        "sCss" : sCss
    });

});