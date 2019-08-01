/**
 *  author：Hidove 余生
 *  mail：i@abcyun.cc
 *  Blog：blog.hidove.cn
 */
$("#Hidove").fileinput({
    language: 'en',//设置语言zh为 Chinese，en 为 English
    uploadUrl: 'kuaiyun.php',//设置上传接口地址
    allowedFileExtensions: ['jpeg', 'jpg', 'png', 'gif', 'bmp'],
    overwriteInitial: false,
    showClose: false,
    maxFileSize: 5000,//最大文件大小
    maxFileCount: 10,//最多上传数
    browseClass:"btn btn-primary", //按钮样式  
}).on("fileuploaded", function (event, data, previewId, index) {
    var form = data.form, files = data.files, extra = data.extra, response = data.response, reader = data.reader;
    if (response.code == '200') {
        if ($("showurl").css("display")) {
            $('#urlcode').append(response.url + "\n");
            $('#htmlcode').append("&lt;img src=\"" + response.url + "\" alt=\"" + files[index].name + "\" title=\"" + files[index].name + "\" /&gt;" + "\n");
            $('#bbcode').append("[img]" + response.url + "[/img]" + "\n");
            $('#markdown').append("![" + files[index].name + "](" + response.url + ")" + "\n");
            $('#markdownlinks').append("[![" + files[index].name + "](" + response.url + ")]" + "(" + response.url + ")" + "\n");
        } else if (response.url) {
            $("#showurl").show();
            $('#urlcode').append(response.url + "\n");
            $('#htmlcode').append("&lt;img src=\"" + response.url + "\" alt=\"" + files[index].name + "\" title=\"" + files[index].name + "\" /&gt;" + "\n");
            $('#bbcode').append("[img]" + response.url + "[/img]" + "\n");
            $('#markdown').append("![" + files[index].name + "](" + response.url + ")" + "\n");
            $('#markdownlinks').append("[![" + files[index].name + "](" + response.url + ")]" + "(" + response.url + ")" + "\n");
        }
    }
});