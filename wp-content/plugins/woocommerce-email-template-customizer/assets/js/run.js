jQuery(document).ready(function ($) {
    'use strict';

    let previewContent = $('.viwec-email-preview-content');

    ViWec.Builder.init();

    const runApp = {
        init() {
            let emailTypeSelect = $('.viwec-set-email-type');
            this.setupPage();
            this.setupPreviewModal();
            this.emailTypeChange();
            this.hideRules(emailTypeSelect.val());
            this.hideElements(emailTypeSelect.val());
            this.addNewTemplate();
            this.attachmentFile();
            this.eximData();
            this.direction();
        },

        setupPage() {
            $(window).bind('beforeunload');

            //Toggle admin menu
            if ($(document).width() <= 1400) {
                $('body').addClass('folded');
            }

            //Remove metabox handle
            $('.hndle').removeClass('hndle');

            //Block enter key
            $('form').bind('keypress', function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            });

            //Hide quick shortcode
            $(document).on('click', function (e) {
                if (!$(e.target).is('.viwec-quick-shortcode-list') && !$(e.target).is('.dashicons.dashicons-menu')) {
                    $('.viwec-quick-shortcode-list').hide();
                    $('.viwec-subject-quick-shortcode ul').hide();
                }
            });

            //Init select2 to rule
            $('.viwec-select2').select2({placeholder: $(this).attr('data-placeholder')});

          /*  $('.viwec-select2-seach-product').select2({
                width: '100%',
                cache: true,
                minimumInputLength: 3,
                ajax: {
                    url: viWecParams.ajaxUrl,
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term,
                            action: 'woocommerce_json_search_products_and_variations',
                            security: wc_enhanced_select_params.search_products_nonce
                        };
                    },
                    processResults: function (data) {
                        return {results: data};
                    },
                },
            });*/
            //Init control panel tab
            $(`#viwec-control-panel .menu .item`).tab();

            $('.viwec-toggle-admin-bar').on('click', function () {
                let _this = $(this);
                _this.toggleClass('dashicons-arrow-left dashicons-arrow-right', 1000);
                $('body.wp-admin').toggleClass('viwec-admin-bar-hidden', 1000);

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {action: 'viwec_change_admin_bar_stt'},
                });
            });

            $('.viwec-quick-add-layout-btn').on('click', function () {
                $('.viwec-layout-list').toggle();
            });
        },

        setupPreviewModal() {
            //Block links
            $('body').on('click', '.viwec-email-preview a, #viwec-email-editor-wrapper a', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            });
        },

        addNewTemplate() {
            if (typeof viWecParams.addNew !== 'undefined') {
                let id = viWecParams.addNew.type || '', style = viWecParams.addNew.style || '';
                if (id && style) {
                    viWecFunctions.doChangeSampleTemplate(id, style);
                }

                $('.viwec-samples-type').val(id).trigger('change');
                $('.viwec-samples-style').val(style).trigger('change');

                delete viWecParams.addNew;
            }
        },

        emailTypeChange() {
            $('.viwec-set-email-type').on('change', function () {
                let emailType = $(this).val();
                runApp.hideRules(emailType);
                runApp.hideElements(emailType);

                ViWec.reloadShortcodes(emailType);
            }).trigger('change');
        },

        hideRules(type) {
            let rules = $('#viwec-box-rule');
            rules.show();
            rules.find('.viwec-setting-row').show();
            let args = viWecParams.hide_rule || '';
            let list = args[type] || '';
            if (!list) return;
            if (list.length === 8) rules.hide();
            for (let el of list) {
                rules.find(`.viwec-setting-row[data-attr=${el}]`).hide();
            }
        },

        hideElements(type) {
            let args = viWecParams.accept_elements || '';
            let list = args[type] || '';

            if (!list) {
                $('#viwec-components-list .viwec-control-btn').parent().removeClass('viwec-hidden');
            } else {
                $('#viwec-components-list .viwec-control-btn').parent().addClass('viwec-hidden');

                for (let el of list) {
                    $(`#viwec-components-list .viwec-control-btn[data-type='${el}']`).parent().removeClass('viwec-hidden');
                }
            }
        },

        eximData() {
            $('.viwec-export-data').on('click', function () {
                let data = getEmailStructure();
                let regex = new RegExp(viWecParams.siteUrl, 'g');
                data = data.replace(regex, '{_site_url}');
                $('#viwec-exim-data').val(data);
            });

            $('.viwec-import-data').on('click', function () {
                let data = $('#viwec-exim-data').val();
                data = data.replace(/{_site_url}/g, viWecParams.siteUrl);
                if (data) {
                    ViWec.viWecDrawTemplate(JSON.parse(data));
                }
            });

            $('.viwec-copy-data').on('click', function () {
                $('#viwec-exim-data').select();
                document.execCommand("copy");
            });
        },

        attachmentFile() {
            $('.viwec-attachment-el').on('click', '.viwec-remove-attachment', function (e) {
                $(this).parent().remove();
            });

            let images = wp.media({multiple: true});
            $('.viwec-add-attachment-file').on('click', function () {
                let list = $('.viwec-attachments-list');
                images.on('select', function (e) {
                    var selection = images.state().get('selection');
                    selection.each(function (attachment) {
                        attachment = attachment.toJSON();
                        let {id, filename} = attachment;
                        if (list.find(`[value=${id}]`).length === 0) {
                            let el = $(`<div class="viwec-attachment-el vi-ui button tiny">
                                        <a href="${viWecParams.uploadUrl}?item=${id}" target="_blank">${filename}</a>
                                        <input type="hidden" name="viwec_attachments[]" value="${id}">
                                        <i class="viwec-remove-attachment dashicons dashicons-no-alt"></i>
                                    </div>`);

                            el.on('click', '.viwec-remove-attachment', function () {
                                el.remove();
                            });

                            list.append(el);
                        }
                    });
                }).open();
            });
        },

        direction() {
            $('.viwec-settings-direction').on('change', function () {
                let dir = $(this).val();
                let editor = $('#viwec-email-editor-content');
                editor.removeClass('viwec-direction-rtl viwec-direction-ltr');
                editor.addClass('viwec-direction-' + dir);
            });
        }
    };

    $('.viwec-show-sub-actions').on('click', function () {
        $('.viwec-actions-back').slideToggle();
    });

    $('.viwec-order-id-test').on('change', function () {
        viWecChange = true;
    });

    $('#viwec-custom-css textarea').on('change', () => {
        viWecChange = true;
    });

    {
        let title = $('#title[name=post_title]'), titlePos,
            viWecQuickSC = $('.viwec-subject-quick-shortcode');

        title.on('focusout', function () {
            titlePos = this.selectionStart;
        });

        viWecQuickSC.on('click', '.dashicons-menu', function () {
            viWecQuickSC.find('ul').html(Object.keys(ViWec.shortcodes).map(sc => `<li>${sc}</li>`));
            viWecQuickSC.find('ul').toggle('fast');
        });

        viWecQuickSC.on('click', 'li', function () {
            let currentText = title.val(),
                sc = $(this).text(), newText;

            if (titlePos) {
                let before = currentText.substr(0, titlePos);
                let after = currentText.substr(titlePos);
                newText = before + sc + after;
            } else {
                newText = currentText + sc;
            }

            title.val(newText).focus();
            $('#title-prompt-text').addClass('screen-reader-text');
            viWecQuickSC.find('ul').toggle('fast');
        });
    }

    //Rebuild viewer from db

    const viWecAttributeGroup = (key) => {
        if (['padding-left', 'padding-right', 'padding-top', 'padding-bottom'].includes(key)) {
            key = 'padding';
        }

        if (['border-top-left-radius', 'border-top-right-radius', 'border-bottom-left-radius', 'border-bottom-right-radius'].includes(key)) {
            key = 'border-radius';
        }

        // if (['border-top-width', 'border-left-width', 'border-bottom-width', 'border-right-width'].includes(key)) {
        //     key = 'border-width';
        // }

        return key;
    };

    const viWecFixColor = (string) => {
        if (string) {
            let patern = /rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?\)/i;
            let rgb = string.match(patern);
            if (rgb) {
                let hex = viWecRgb2hex(rgb[0]);
                string = string.replace(patern, hex);
            }
            let pattern000 = /rgba?[\s+]?\([\s+]?(0)[\s+]?,[\s+]?(0)[\s+]?,[\s+]?(0)[\s+]?,[\s+]?(0)[\s+]?\)/i;
            let rgba000 = string.match(pattern000);
            if (rgba000) {
                string = 'transparent';
            }
            string = string.replace(/"/gi, "");
            return string;
        }
        return '';
    };

    const viWecGetElCss = (element, type) => {
        let component = ViWec.Components._components[type],
            properties = component.properties,
            content = {}, attrs = {}, childStyle = {}, style = {};

        style['width'] = $(element).css('width');

        for (let i in properties) {
            if (properties[i].htmlAttr) {
                let _element = properties[i].target ? $(element).find(properties[i].target) : $(element);
                let _key = viWecAttributeGroup(properties[i].key);

                switch (properties[i].htmlAttr) {
                    case 'innerHTML':
                        content[_key] = _element.html();
                        break;

                    case 'style':
                        let css;
                        css = ViWec.StyleManager.getStyle(_element, properties[i], _key); //_element.css(_key)
                        if (css) style[_key] = viWecFixColor(css);

                        break;

                    case 'childStyle':
                        if (!childStyle[properties[i].target]) childStyle[properties[i].target] = {};
                        let childCss = ViWec.StyleManager.getStyle(_element, properties[i], _key);

                        if (childCss) childStyle[properties[i].target][_key] = viWecFixColor(childCss);

                        break;

                    default :
                        let attr = _element.attr(properties[i].htmlAttr),
                            defaultValue = properties[i].default ? properties[i].default : '';
                        attr = attr ? attr : defaultValue;
                        attrs[_key] = attr;
                        break;
                }
            }
        }

        return {type: type, style: style, content: content, attrs: attrs, childStyle: childStyle};
    };

    const viWecGetRowCss = (element, type) => {

        let component = ViWec.Components._components[type],
            properties = component ? component.properties : '',
            style = {};

        if (properties) {
            for (let i in properties) {
                if (properties[i].htmlAttr) {
                    let _key = viWecAttributeGroup(properties[i].key);
                    // let css = element.css(_key);
                    let el = (element.get(0)), css;

                    if (el.style && el.style.length > 0 && el.style[_key])//check inline
                        css = el.style[_key];
                    else if (el.currentStyle)	//check defined css
                        css = el.currentStyle[_key];
                    else if (window.getComputedStyle) {
                        css = document.defaultView.getDefaultComputedStyle ?
                            document.defaultView.getDefaultComputedStyle(el, null).getPropertyValue(_key) :
                            window.getComputedStyle(el, null).getPropertyValue(_key);
                    }

                    if (css) style[_key] = viWecFixColor(css);

                }
            }
            style['width'] = element.css('width')
        }
        return style;
    };

    const getEmailStructure = () => {
        let dataArray = {}, container = $('#viwec-email-editor-wrapper'),
            editorContainer = $('#viwec-email-editor-content');

        dataArray['style_container'] = {
            'background-color': viWecFixColor(container.css('background-color')),
            'background-image': container.css('background-image'),
            'width': editorContainer.width(),
            'responsive': editorContainer.attr('data-responsive')
        };

        dataArray['rows'] = {};

        $(viWecEditorArea).find('.viwec-block').each(function (rowIndex, rowBlock) {
            let row = $(rowBlock).find('.viwec-layout-row');
            let templateBlock = $(rowBlock).find('.viwec-template-block');

            if (row.length) {
                let type = $(row).attr('data-type'), dataCols = $(row).attr('data-cols'),
                    rowOuterStyle = viWecGetRowCss($(row), type);
                rowOuterStyle.width = '100%';
                dataArray['rows'][rowIndex] = {props: {style_outer: rowOuterStyle, type: type, dataCols: dataCols}, cols: {}}; //style_inner: rowInnerStyle,

                //get columns
                let col = $(row).find('.viwec-column-sortable');
                if (col.length) {
                    col.each(function (colIndex, col) { //loop cols
                        // let colStyle = viWecFixColor($(col).attr('style'));
                        let colStyle = viWecGetRowCss($(col), 'layout/grid1cols');
                        dataArray['rows'][rowIndex]['cols'][colIndex] = {props: {style: colStyle}, elements: {}};

                        //get elements
                        let elements = $(col).find('.viwec-element');
                        if (elements.length) {
                            elements.each(function (elIndex, element) {
                                let type = $(element).data('type');
                                dataArray['rows'][rowIndex]['cols'][colIndex]['elements'][elIndex] = viWecGetElCss(element, type);
                            })
                        }
                    })
                }
            }

            if (templateBlock.length) {
                dataArray['rows'][rowIndex] = templateBlock.attr('data-block');
            }

        });//loop rows

        return JSON.stringify(dataArray);
    };

    $('#save-post').on('click', function () {
        $("input[name=post_status]").val('draft');
    });

    $('form').on('submit', function (e) {
        $(window).unbind('beforeunload');
        $("<input/>").attr({type: 'hidden', name: 'viwec_email_structure', value: getEmailStructure()}).appendTo("form#post");
        return true;
    });

    function viWecPreview() {
        let data = {
                action: 'viwec_preview_template',
                nonce: viWecParams.nonce,
                data: getEmailStructure(),
                order_id: $('.viwec-order-id-test').val(),
                direction: $('.viwec-settings-direction').val(),
                custom_css: $('#viwec-custom-css textarea').val(),
                email_type: $('.viwec-set-email-type').val()
            },
            button = $(this),
            modal = $('.vi-ui.modal');

        if (viWecChange === false) {
            modal.modal('show');
            if (button.hasClass('mobile')) {
                previewContent.addClass('viwec-mobile-preview');
            }
            if (button.hasClass('desktop')) {
                previewContent.removeClass('viwec-mobile-preview');
            }
        } else {
            $.ajax({
                url: viWecParams.ajaxUrl,
                type: 'post',
                data: data,
                beforeSend: function () {
                    button.addClass('loading').unbind();
                },
                success: function (res) {
                    if (res) {
                        modal.find('.viwec-email-preview-content').html(res);
                        modal.modal('show');

                        $('.viwec-email-preview-content a').on('click', function (e) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                        });
                    }
                },
                error: function (res) {
                    console.log(res);
                },
                complete: function () {
                    button.removeClass('loading');
                    button.bind('click', viWecPreview);
                    viWecChange = false;
                    if (button.hasClass('mobile')) {
                        previewContent.addClass('viwec-mobile-preview');
                    }
                    if (button.hasClass('desktop')) {
                        previewContent.removeClass('viwec-mobile-preview');
                    }
                }
            });
        }
    }

    $('.viwec-preview-email-btn').on('click', viWecPreview);

    function viWecSendTestEmail() {
        let button = $('.viwec-send-test-email-btn');
        let email = $('.viwec-to-email').val(), attachments = [];

        if (!email) {
            alert('Please input your email');
            $('.vi-ui.modal').modal('hide');
            return;
        }

        $('.viwec-attachment-el input').each(function () {
            if ($(this).val()) attachments.push($(this).val());
        });

        let data = {
            action: 'viwec_send_test_email',
            nonce: viWecParams.nonce,
            data: getEmailStructure(),
            order_id: $('.viwec-order-id-test').val(),
            email: email,
            attachments: attachments,
            direction: $('.viwec-settings-direction').val(),
            custom_css: $('#viwec-custom-css textarea').val(),
            email_type: $('.viwec-set-email-type').val(),
            post_ID: $('#post_ID').val(),
        };

        $.ajax({
            url: viWecParams.ajaxUrl,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function () {
                button.addClass('loading').unbind();
            },
            success: function (res) {
                let color = res.success ? '#00DA00' : 'red';
                viWecNoticeBox(res.data, color);
            },
            complete: function () {
                button.removeClass('loading').bind('click', viWecSendTestEmail);
                $('.vi-ui.modal').modal('hide');
            }
        });
    }

    $('.viwec-send-test-email-btn').on('click', viWecSendTestEmail);

    $('.viwec-mobile-view').on('click', function () {
        previewContent.addClass('viwec-mobile-preview');
    });

    $('.viwec-pc-view').on('click', function () {
        previewContent.removeClass('viwec-mobile-preview');
    });

    ViWec.renderRows = (rows, addEvent = true) => {
        let result = [];
        for (let rowIndex in rows) {
            if (typeof rows[rowIndex] === 'string' || typeof rows[rowIndex] === 'number') {
                let templateID = +rows[rowIndex],
                    selectedOption = viWecParams.templateBlocks.find(el => +el.id === +templateID);

                if (selectedOption) {
                    let renderData = selectedOption.data,
                        row = $(`<div class="viwec-block"><div class="viwec-template-block" data-type="blocks" data-block="${templateID}"></div></div>`),
                        _rows = ViWec.renderRows(renderData.rows, false);

                    row.find('.viwec-template-block').append(_rows);
                    row.handleRow();
                    // row.find('.viwec-edit-outer-row-btn').remove();

                    result.push(row);
                }
            } else {

                if ($.isEmptyObject(rows[rowIndex]['cols'])) continue;

                let row = $(viWecTmpl('viwec-block', {type: rows[rowIndex].props.type, colsQty: rows[rowIndex].props.dataCols}));

                if (!addEvent) row.find('.viwec-column-control').remove();

                row.find('.viwec-layout-row').css(rows[rowIndex].props.style_outer);

                row.find('.viwec-column').each(function (colIndex) {
                    let col = $(this);
                    if (!$.isEmptyObject(rows[rowIndex]['cols'][colIndex].elements)) {
                        col.removeClass('viwec-column-placeholder')
                    }

                    let colStyle = rows[rowIndex]['cols'][colIndex].props.style;
                    delete colStyle.width;

                    col.find('.viwec-column-sortable').css(colStyle);

                    for (let elIndex in rows[rowIndex]['cols'][colIndex]['elements']) {
                        let el = rows[rowIndex]['cols'][colIndex]['elements'][elIndex],
                            type = el.type,
                            style = el.style,
                            content = el.content,
                            attrs = el.attrs.length !== 0 ? el.attrs : {},
                            childStyle = el.childStyle,
                            component = ViWec.Components._components[type];

                        delete style.width;

                        if (typeof component === 'undefined') continue;

                        let properties = component.properties,
                            element = $(`<div class='viwec-element' data-type="${type}"></div>`).append(component.html);

                        for (let i in properties) {
                            if (properties[i].htmlAttr && properties[i].visible !== false) {
                                let _element = properties[i].target ? element.find(properties[i].target) : element;
                                switch (properties[i].htmlAttr) {
                                    case 'innerHTML':
                                        if (properties[i].renderShortcode) {
                                            let clone = _element.clone();
                                            clone = clone.removeClass().html(ViWec.viWecReplaceShortcode(content[properties[i].key])).addClass('viwec-text-view');
                                            _element.html(content[properties[i].key]).hide();
                                            _element.after(clone);
                                        } else {
                                            _element.html(content[properties[i].key]);
                                        }
                                        if (typeof properties[i].onChange === 'function') {
                                            properties[i].onChange(_element, content[properties[i].key]);
                                        }
                                        break;

                                    case 'style':
                                        if (style) _element.css(style);
                                        break;

                                    case 'childStyle':
                                        if (childStyle[properties[i].target]) _element.css(childStyle[properties[i].target]);
                                        break;

                                    default:
                                        _element.attr(properties[i].htmlAttr, attrs[properties[i].key]);
                                        if (typeof properties[i].onChange === 'function') {
                                            let viewValue = ViWec.viWecReplaceShortcode(attrs[properties[i].key]);
                                            properties[i].onChange(_element, attrs[properties[i].key], viewValue);
                                        }
                                        break;
                                }
                            }
                        }

                        if (addEvent) element.handleElement();

                        col.find('.viwec-column-sortable').append(element);
                    }

                    if (addEvent) {
                        col.handleColumn();
                        col.find('.viwec-column-sortable').columnSortAble();
                    }

                    row.find('.viwec-flex').append(col);
                });

                if (addEvent) {
                    row.handleRow();
                } else {
                    row.removeClass('viwec-block');
                    row.find('.viwec-element').removeClass('viwec-element');
                    row.find('.viwec-column-sortable').removeClass('viwec-column-sortable');
                    row.find('.viwec-layout-row').removeClass('viwec-layout-row');
                }

                result.push(row);
            }
        }

        return result;
    };

    ViWec.viWecDrawTemplate = (viWecTemplate) => {
        if (!viWecTemplate) {
            return;
        }
        $(viWecEditorArea).empty();

        let bg = {
            backgroundColor: viWecTemplate['style_container']['background-color'] || '',
            backgroundImage: viWecTemplate['style_container']['background-image'] || ''
        };

        $('#viwec-email-editor-wrapper').css(bg);
        $('#viwec-email-editor-content').css({width: viWecTemplate['style_container']['width']}).attr('data-responsive', viWecTemplate['style_container']['responsive'] || 380);

        $(viWecEditorArea).append(ViWec.renderRows(viWecTemplate['rows']));
    };

    if (typeof viWecLoadTemplate !== 'undefined') {
        let viWecTemplate = JSON.parse(viWecLoadTemplate);
        ViWec.viWecDrawTemplate(viWecTemplate);
    }

    $('#viwec-element-search input.viwec-search').on('keyup', function () {
        let keyword = $(this).val().toUpperCase(), li = $('#viwec-components-list li');
        for (let i = 0; i < li.length; i++) {
            let a = $(li[i]).find('.viwec-ctrl-title');
            let txtValue = a.text();
            if (txtValue.toUpperCase().indexOf(keyword) > -1) {
                $(li[i]).show();
            } else {
                $(li[i]).hide();
            }
        }
    });

    runApp.init();

});