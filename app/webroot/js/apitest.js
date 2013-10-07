$(document).ready(function() {
    writeMessage();
    $(".test_form").find("input").blur(function() {
        writeMessage();
    });
    $(".test_form").find("input").keypress(function(event) {
        if (event.which == 13 || event.which == 0) {
            writeMessage();
        }
    });
    $("#submit_button").click(function(event){
        var message = $("textarea#query_minified_display").val();
        $.post(
            get_tatoeba_root_url()+"/jsonrpc_api/search",
            message
        );
    });
    $(".search_query_display").bind("paste", function(event){
        $(".test_form").find("input").find("id!=submit_button").attr("disabled", true);
    });
});


function writeMessage()
{
    var SPACE = "    ";
    var message = "{\n";
    var messageMini = "{";  //Minified message
    //JSONRPC header fields
    var inputs = $("#HeaderFieldset").find("input");
    var length = inputs.length;
    var count = 0;
    while (count < length) {
        var inr = $(inputs[count]);
        message += SPACE;
        message += "\""+inr.attr("name")+"\" : \""+inr.val()+"\",\n";
        messageMini += "\""+inr.attr("name")+"\":\""+inr.val()+"\",";
        count++;
    }
    //JSONRPC params fields
    message += SPACE + "\""+"params"+"\" : {\n";
    messageMini += "\""+"params"+"\" : {";
    var params = $("#ParamsFieldset").find("input");
    var numParams = params.length;
    count = 0;
    while(count < numParams) {
        var inq = $(params[count]);
        message += SPACE+SPACE;
        if (count == numParams-1) {
            message += "\""+inq.attr("name")+"\" : \""+inq.val()+"\"\n";
            messageMini += "\""+inq.attr("short")+"\":\""+inq.val()+"\"";
        } else {
           message += "\""+inq.attr("name")+"\" : \""+inq.val()+"\",\n";
           messageMini += "\""+inq.attr("short")+"\":\""+inq.val()+"\",";
        }
        count++;
    }
    //close message
    message += SPACE+"}\n";
    message += "}";
    messageMini += "}}";
    var params = $("textarea#query_display");
    params.val(message);
    var paramsMini = $("textarea#query_minified_display");
    paramsMini.val(messageMini);
}