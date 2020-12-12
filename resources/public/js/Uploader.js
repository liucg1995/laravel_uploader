Utils = {};
Utils.printf = function () {
    var num = arguments.length;
    var oStr = arguments[0];
    for (var i = 1; i < num; i++) {
        var pattern = "\\{" + (i - 1) + "\\}";
        var re = new RegExp(pattern, "g");
        oStr = oStr.replace(re, arguments[i]);
    }
    return oStr;
};
Utils.startsWith = function (needle, str) {
    return str.indexOf(needle) === 0;
};

function Uploader(selector) {
    this.selector = selector;

    this.uploader = null;

    this.currentQty = 0;

    this.init = function () {
        WebUploader.Mediator.installTo(this);
        this.create();
        this.bind();
    };

    this.create = function () {
        var _this = this;
        this.options = JSON.parse(this.getAttr('data-options'));
        this.name = this.getAttr('data-name');
        this.max = parseInt(this.getAttr('data-max'));
        this.extensions = this.getAttr('data-accept');

        this.uploader = WebUploader.create({
            server: _this.options.url,
            auto: true,
            pick: {
                id: this.selector.find('.picker'),
                label: ' ',
                multiple: true
            },
            accept: {
                extensions: this.extensions
            },
            resize: false,
            fileVal: _this.options.fileName
        });
    };

    this.bind = function () {
        var _this = this;
        this.on('qtyChanged', function () {
            var picker = $('.picker');
            this.currentQty < this.max ? picker.show() : picker.hide();
        });

        $('.uploader-list').on('click', '.delete', function () {
            $(this).parent().remove();
            _this.currentQty--;
            _this.trigger('qtyChanged');
        });
        this.uploader.on('uploadBeforeSend', function (block, data, headers) {
            var file = block.file;
            var params = _this.options.params;
            var header = _this.options.header;

            if (params.length !== 0) {
                for (var k in params) {
                    var guid = WebUploader.Base.guid();
                    data[k] = params[k].toString().replace('{s_filename}', guid + '.' + file.ext);

                    if (params[k].toString().indexOf('{s_filename}') !== -1) {
                        _this.options.params[k] = data[k];
                    }
                }
            }
            if (header.length !== 0) {
                for (var h in header) {
                    headers[h] = header[h];
                }
            }
        });
        this.uploader.on('fileQueued', function (file) {
            if (Utils.startsWith('image', file.type)) {
                var _li = '<div id="{0}" class="img-item"><div class="delete"></div><img class="img" src="{1}"><div class="wrapper">0%</div></div>';
                _this.uploader.makeThumb(file, function (error, src) {
                    if (error) return;
                    _this.selector.find('.picker').before(Utils.printf(_li, file.id, src));
                }, 75, 75);
            } else {
                var _li = '<div id="{0}" class="img-item"><p>{1}</p><div class="wrapper">0%</div></div>';
                _this.selector.find('.picker').before(Utils.printf(_li, file.id, file.ext.toUpperCase()));
            }
        });

        this.uploader.on('uploadProgress', function (file, percentage) {
            $('#' + file.id).find('.wrapper').text(parseInt(percentage * 100) + '%');
        });

        this.uploader.on('uploadSuccess', function (file, response) {


            console.log(file, response);

            console.log(this.options);


            var _input = '<input type="hidden" name="{0}[]" value="{1}" />';
            if (parseInt(_this.max) === 1) {
                // _input = _input.replace('[]', '');
            }
            if (_this.options.responseKey.indexOf('.') !== -1) {     //包含
                var keys = _this.options.responseKey.split('.');
                var filename = response;
                keys.map(function (item) {
                    filename = filename[item];
                });
            } else {
                var filename = response[_this.options.responseKey];
            }
            if (filename === undefined) {    //尝试从参数中解析
                filename = _this.options.params[_this.options.responseKey];
            }
            if (filename !== undefined) {    //尝试从参数中解析
                // _this.selector.find('#'+file.id).append(Utils.printf(_input, _this.name, filename));
                _this.selector.find('#' + file.id).find('.wrapper').hide();
                _this.currentQty++;
                _this.trigger('qtyChanged');

                if (this.options.threads > 1) {
                    _this.selector.find('#' + file.id).append('<input type="hidden" id="' + _this.name + '" data-id="' + response.alpha_id + '"  name="' + _this.name + '[' + response.alpha_id + ']" value="' + response.key + '" />')
                } else {
                    _this.selector.find('#' + file.id).append('<input type="hidden" name="' + _this.name + '" value="' + response.key + '" />');
                    _this.selector.find('#' + file.id).append('<input type="hidden" id="' + _this.name + '" data-id="' + response.alpha_id + '" name="' + _this.name + '_id" value="' + response.alpha_id + '" />');

                }


            }
        });


        this.uploader.on('error', function (code, file) {
            console.log(code, file);
            _this.selector.find('#' + file.id).find('.wrapper').addClass('error').text('error');
            var name = file.name;
            var str = "";
            switch (code) {
                case "F_DUPLICATE":
                    str = name + "文件重复";
                    alert(str);
                    break;
                case "Q_TYPE_DENIED":
                    str = name + "文件类型 不允许";
                    alert(str);
                    break;
                case "F_EXCEED_SIZE":
                    var imageMaxSize = 9;//通过计算
                    str = name + "文件大小超出限制" + imageMaxSize + "M";
                    alert(str);
                    break;
                case "Q_EXCEED_SIZE_LIMIT":
                    alert("超出空间文件大小");

                    break;
                case "Q_EXCEED_NUM_LIMIT":
                    alert("抱歉，超过每次上传数量图片限制");
                    break;
                default:
                    str = name + " Error:" + code;
                    alert(str);
                    break;
            }

        });
    };

    this.getAttr = function (name) {
        return this.selector.attr(name);
    }
}

var ups = $("div[id^='uploader_']");
for (var i = 0; i < ups.length; i++) {
    var uploader = new Uploader($('#' + ups[i].id));
    uploader.init();
}

