(function() {
    var stylesheet = [
".ddice_form {display:block;position:absolute;top:10px;left:10px;}"
,".ddice_form UL,LI,DL,DD,DIV,BUTTON {margin: 0 0 0 0;padding:0 0 0 0;}"
,".ddice_form BUTTON {width:30px; font-size:8pt;vertical-align:middle;height:30px;}"
,".ddice_form {border:1px solid #c0c0c0; box-shadow: inset 0 1px 2px #e4e4e4;width:330px;font-size:9pt;background: #FFFFFF;}"
,".ddice_form .ddice_dices LI {display:inline;}"
,".ddice_form DL DT {float:left;width:70px;margin: 0 10px 0 10px;padding: 5px 0 5px 0;}"
,".ddice_form DL DD {width:230px;margin:0 0 0 90px;padding: 5px 0 5px 0;}"
,".ddice_form .ddice_close_div {text-align:right;padding: 5px;font-size:8pt;}"
,".ddice_form .ddice_close_div A {text-decoration:none;}"
    ].join('\n');
    
    $('<STYLE type="text/css"></STYLE>').appendTo("HEAD").html(stylesheet);
}());