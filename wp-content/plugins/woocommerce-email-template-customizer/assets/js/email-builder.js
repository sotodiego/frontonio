'use strict';

let styleSection = 'style',
    contentSection = 'content',
    viWecEditorArea = '#viwec-email-editor-content',
    viWecChange = true,

    viWecFontWeightOptions = [
        {id: 300, text: 300},
        {id: 400, text: 400},
        {id: 500, text: 500},
        {id: 600, text: 600},
        {id: 700, text: 700},
        {id: 800, text: 800},
        {id: 900, text: 900}
    ],

    viWecAlignmentOptions = [
        {value: "left", title: "Left", icon: "dashicons dashicons-editor-alignleft", checked: true,},
        {value: "center", title: "Center", icon: "dashicons dashicons-editor-aligncenter", checked: false,},
        {value: "right", title: "Right", icon: "dashicons dashicons-editor-alignright", checked: false,}
    ],

    viWecFontFamilyOptions = [
        {id: "Roboto, RobotoDraft, Helvetica, Arial, sans-serif", text: 'Roboto'},
        {id: "'andale mono', monospace", text: 'Andale Mono'},
        {id: 'arial, helvetica, sans-serif', text: 'Arial'},
        {id: "'arial black', sans-serif", text: 'Arial Black'},
        {id: "'book antiqua', palatino, serif", text: 'Book Antiqua'},
        {id: "'comic sans ms', sans-serif", text: 'Comic Sans MS'},
        {id: "'courier new', courier, monospace", text: 'Courier New'},
        {id: 'georgia, palatino, serif', text: 'Georgia'},
        {id: 'helvetica, arial, sans-serif', text: 'Helvetica'},
        {id: 'impact, sans-serif', text: 'Impact'},
        {id: 'symbol', text: 'Symbol'},
        {id: 'tahoma, arial, helvetica, sans-serif', text: 'Tahoma'},
        {id: 'terminal, monaco, monospace', text: 'Terminal'},
        {id: "'times new roman', times, serif", text: 'Times New Roman'},
        {id: "'trebuchet ms', geneva, sans-serif", text: 'Trebuchet MS'},
        {id: 'verdana, geneva, sans-serif', text: 'Verdana'},
        {id: 'webdings', text: 'Webdings'},
        {id: "wingdings, 'zapf dingbats'", text: 'Wingdings'},
    ];

if (ViWec === undefined) var ViWec = {};

{
    let cache = {};
    window.viWecTmpl = function viWecTmpl(str, data) {
        var fn = /^[-a-zA-Z0-9]+$/.test(str) ? cache[str] = cache[str] || viWecTmpl(document.getElementById(str).innerHTML) :
            new Function("obj", "var p=[],print=function(){p.push.apply(p,arguments);};" + "with(obj){p.push('" +
                str.replace(/[\r\t\n]/g, " ")
                    .split("{%").join("\t")
                    .replace(/((^|%})[^\t]*)'/g, "$1\r")
                    .replace(/\t=(.*?)%}/g, "',$1,'")
                    .split("\t").join("');")
                    .split("%}").join("p.push('")
                    .split("\r").join("\\'")
                + "');}return p.join('');");
        // Provide some basic currying to the user
        return data ? fn(data) : fn;
    };
}

ViWec.shortcodes = {...viWecParams.commonShortcodes};
ViWec.reloadShortcodes = (type) => {
    let typedSc = viWecParams.typedShortcodes[type] || {};
    ViWec.shortcodes = {...viWecParams.commonShortcodes, ...typedSc};
};

jQuery(document).ready(function ($) {
    window.viWecNoticeBox = (text, color, time = 3000) => {
        color = color || 'white';
        let box = $('#viwec-notice-box');
        box.text(text).css({'color': color, 'bottom': 0});
        setTimeout(function () {
            box.css({'bottom': '-50px'});
        }, time);
    };

    $.fn.handleRow = function () {
        if (this.find('.viwec-layout-handle-outer').length === 0) {
            this.append(viWecTmpl('viwec-input-handle-outer', {}));
        }

        this.on('click', '.viwec-delete-row-btn', () => {
            this.remove();
            ViWec.Builder.clearTab();
            ViWec.Builder.activeTab('components');
        });

        this.on('click', '.viwec-duplicate-row-btn', () => {
            let clone = this.clone();
            clone.find('.viwec-column-sortable').columnSortAble();
            clone.handleRow().handleColumn();
            this.after(clone);
        });

        this.on('click', '.viwec-edit-outer-row-btn', () => {
            ViWec.Builder.removeFocus();
            this.addClass('viwec-block-focus');
            let row = this.find('.viwec-layout-row');
            let block = this.find('.viwec-template-block');
            ViWec.Builder.selectedEl = row.length ? row : block;
            ViWec.Builder.loadLayoutControl();

            if (block.length) {
                let blockID = block.attr('data-block');
                if (blockID) window.open(`post.php?post=${blockID}&action=edit`)
            }
        });

        this.on('click', '.viwec-copy-row-btn', function () {
            let row = $(this).closest('.viwec-block');
            row = row.prop('outerHTML');
            localStorage.setItem('viwecCopyRow', row);
            viWecNoticeBox('Copied');
        });

        this.on('click', '.viwec-paste-row-btn', function () {
            let row = localStorage.getItem('viwecCopyRow');
            if (row) {
                row = $(row);
                row.find('.viwec-column-sortable').columnSortAble();
                row.handleRow().handleColumn();
                $(this).closest('.viwec-block').after(row);
            }
        });

        return this;
    };

    $.fn.handleElement = function () {
        this.append(`<div class="viwec-element-handle">
                <span class="dashicons dashicons-welcome-add-page viwec-copy-element-btn" title="Copy"></span>
                <span class="dashicons dashicons-admin-page viwec-duplicate-element-btn" title="Duplicate"></span>
                <span class="dashicons dashicons-no-alt viwec-delete-element-btn" title="Delete"></span></div>`);
    };

    $.fn.columnSortAble = function () {
        $(this).sortable({
            cursor: 'move',
            cursorAt: {left: 40, top: 18},
            placeholder: 'viwec-placeholder',
            connectWith: ".viwec-column-sortable",
            thisColumn: '',
            accept: '.viwec-content-draggable',
            start(ev, ui) {
                ui.helper.addClass('viwec-is-dragging');
                this.thisColumn = ui.helper.closest('.viwec-column');
            },
            stop(ev, ui) {
                let style = ui.item.get(0).style;

                style.position = style.top = style.left = style.right = style.bottom = style.height = style.width = '';

                ui.item.removeClass('viwec-is-dragging');

                if (ui.item.offsetParent().find('.viwec-element').length) {
                    ui.item.offsetParent().removeClass('viwec-column-placeholder');
                }

                if (!(this.thisColumn.find('.viwec-element').length)) {
                    this.thisColumn.addClass('viwec-column-placeholder');
                }

                ui.item.trigger('click');
                viWecChange = true;
            }
        });
    };

    $.fn.handleColumn = function () {
        this.on('click', (e) => {
            if (this.hasClass('viwec-column-placeholder') || this.find('.viwec-column-placeholder').length) {
                ViWec.Builder.removeFocus();
                ViWec.Builder.selectedEl = this.find('.viwec-column-sortable').addClass('viwec-block-focus');
                ViWec.Builder.loadLayoutControl('editColumn');
            }
        });

        this.on('click', '.viwec-column-edit', () => {
            ViWec.Builder.removeFocus();
            ViWec.Builder.selectedEl = this.find('.viwec-column-sortable').addClass('viwec-block-focus');
            ViWec.Builder.loadLayoutControl('editColumn');
        });

        this.on('click', '.viwec-column-paste', function () {
            let item = localStorage.getItem('viwecCopy');
            if (item) {
                item = $(item);
                $(this).closest('.viwec-column').find('.viwec-column-sortable').append(item);
            }
        });

        return this;
    };

    ViWec.viWecPreventXSS = (text) => {
        let $reg, match;
        //removing <script> tags
        text.replace(/[<][^<]*script.*[>].*[<].*[\/].*script*[>]/i,"");
        $reg = /[<][^<]*script.*[>].*[<].*[\/].*script*[>]/i;
        match = $reg.exec(text);
        if (match && match?.input && typeof match[0] !== "undefined"){
            text = match.input.replace(match[0],'');
        }
        //removing inline js events
        text.replace(/([ ]on[a-zA-Z0-9_-]{1,}=\".*\")|([ ]on[a-zA-Z0-9_-]{1,}='.*')|([ ]on[a-zA-Z0-9_-]{1,}=.*[.].*)/,"");
        $reg = /([ ]on[a-zA-Z0-9_-]{1,}=\".*\")|([ ]on[a-zA-Z0-9_-]{1,}='.*')|([ ]on[a-zA-Z0-9_-]{1,}=.*[.].*)/;
        match = $reg.exec(text);
        if (match && match?.input && typeof match[0] !== "undefined"){
            text = match.input.replace(match[0],'');
        }
        //removing inline js
        text.replace(/[ ]src.*=[\"](.*javascript:.*|'.*javascript:.*'|.*javascript:.*)[\"]/i,"");
        $reg = /[ ]src.*=[\"](.*javascript:.*|'.*javascript:.*'|.*javascript:.*)[\"]/i;
        match = $reg.exec(text);
        if (match && match?.input && typeof match[1] !== "undefined"){
            text = match.input.replace(match[1],'');
        }
        text.replace(/[ ]href.*=[\"](.*javascript:.*|'.*javascript:.*'|.*javascript:.*)[\"]/i,"");
        $reg = /[ ]href.*=[\"](.*javascript:.*|'.*javascript:.*'|.*javascript:.*)[\"]/i;
        match = $reg.exec(text);
        if (match && match?.input && typeof match[1] !== "undefined"){
            text = match.input.replace(match[1],'');
        }
        return text;
    }
    ViWec.viWecReplaceShortcode = (text) => {
        if (!text || typeof text !== 'string') return text;
        var re = new RegExp(Object.keys(ViWec.shortcodes).join("|"), "gm");
        text = text.replace(re, function (matched) {
            return ViWec.shortcodes[matched];
        });
        return ViWec.viWecPreventXSS(text);
    };

    ViWec.Components = {
        _categories: {},
        _components: {
            baseProp: {}
        },

        init() {
            let categories = [
                {id: 'sample', name: 'Sample', page: 'template'},
                {id: 'layout', name: 'Layout'},
                {id: 'content', name: 'Basic content'},
                {id: 'recover', name: 'Default template', page: 'template'},
            ];

            for (let category of categories) {
                if (category.page && category.page === 'template' && window.pagenow !== 'viwec_template') continue;
                this.registerCategory(category.id, category.name);
            }

        },

        registerCategory(id, name) {
            if (!this._categories[id]) this._categories[id] = {name: name, elements: []};
        },

        get(type) {
            return this._components[type];
        },

        add(data) {
            let categoryType = data.category || 'content';
            if (this._categories[categoryType]) this._categories[categoryType].elements.push(data.type);

            if (data.inheritProp) {
                let inheritProperties = [];
                for (let property of data.inheritProp) {
                    if (this._components.baseProp[property]) {
                        inheritProperties = [...inheritProperties, ...this._components.baseProp[property].properties];
                    }
                }

                if (!data.properties) data.properties = [];
                data.properties = [...data.properties, ...inheritProperties];
            }

            this._components[data.type] = data;
        },

        addBaseProp(data) {
            this._components.baseProp[data.type] = data;
        },

        render(type) {
            let component = this._components[type], section, attributesArea = $('#viwec-attributes-list');

            if (!component) return;

            //set to viewer
            const bindOnChangeToViewer = function (component, property, element) {
                return property.input.on('propertyChange', function (event, value, input) {
                    viWecChange = true;
                    let viewValue = ViWec.viWecReplaceShortcode(value);

                    if (property.outputValue) value = property.outputValue;

                    if (property.htmlAttr) {
                        switch (true) {
                            case ["style", 'childStyle'].indexOf(property.htmlAttr) > -1:
                                let unit = property.unit ? property.unit : '';
                                element = ViWec.StyleManager.setStyle(element, property.key, value, unit);
                                break;

                            case property.htmlAttr === 'innerHTML':
                                if (property.renderShortcode) {
                                    let clone = element.clone();
                                    element = element.html(value).hide();

                                    let virElement = element.parent().find('.viwec-text-view');
                                    if (virElement.length === 0) {
                                        clone = clone.removeClass().html(viewValue).addClass('viwec-text-view');
                                        element.after(clone);
                                    } else {
                                        virElement.html(viewValue);
                                    }
                                } else {
                                    element.html(value);
                                }
                                break;

                            case property.htmlAttr === 'data-block':
                                let selectedOption = viWecParams.templateBlocks.find(el => +el.id === +value),
                                    renderData = selectedOption.data;

                                if (renderData) {
                                    let blockHtml = ViWec.renderRows(renderData.rows, false);
                                    element.html(blockHtml);
                                    element = element.attr(property.htmlAttr, value);
                                }

                                break;

                            default:
                                element = element.attr(property.htmlAttr, value);
                                break;

                        }
                    }

                    if (typeof component.onChange === 'function') {
                        element = component.onChange(element, property, value, input);
                    }
                    if (typeof property.onChange === 'function') {
                        element = property.onChange(element, value, viewValue, input, component, property);
                    }

                    return element;
                });
            };

            let currentKey = '';

            //render control
            if (component.name) attributesArea.append(`<div id="viwec-component-name">Component: ${component.name}</div>`);

            for (let i in component.properties) {
                var property = component.properties[i];
                var element = ViWec.Builder.selectedEl;
                let value;

                if (property.visible === false || property.target && !element.find(property.target).length) continue;
                if (property.target && element.find(property.target).length) element = element.find(property.target);

                if (property.data) {
                    property.data["key"] = property.key;
                    if (property.name) property.data["header"] = property.name;
                } else {
                    property.data = {"key": property.key};
                    if (property.name) property.data["header"] = property.name;
                }

                if (!property.inputType) continue;

                if (property.inputType.hasOwnProperty('init')) {
                    property.input = property.inputType.init(property.data);
                }

                if (property.init) {
                    property.inputType.setValue(property.init(element.get(0)));
                } else if (property.htmlAttr) {
                    if (property.htmlAttr === "style") {
                        value = ViWec.StyleManager.getStyle(element, property);
                    } else if (property.htmlAttr === "childStyle") {
                        value = ViWec.StyleManager.getStyle(element, property);
                    } else if (property.htmlAttr === "innerHTML") {
                        value = element.html();
                    } else {
                        value = element.attr(property.htmlAttr);
                    }

                    if (!value && property.default) {
                        value = property.default;
                    }

                    if (value) {
                        property.inputType.setValue(value); //set to control
                    }
                }

                if (property.input) {
                    bindOnChangeToViewer(component, property, element);
                }

                section = property.section ? property.section : '';
                if (section) {

                    if (attributesArea.find(`.viwec-${section}`).length === 0) {
                        attributesArea.append(`<div class="viwec-${section} vi-ui accordion styled fluid">
                                                <div class="title active">
                                                    <i class="dropdown icon"></i>
                                                    ${section.replace(/^./, section[0].toUpperCase())}
                                                </div>
                                                <div class="content active ${section}-properties">
                                            </div></div>`);
                    }

                    if (property.inputType === SectionInput) {
                        attributesArea.find(`.viwec-${section} .${section}-properties`).append(viWecTmpl("viwec-input-sectioninput", property.data));
                        currentKey = property.key ? property.key : currentKey;
                    } else if (property.label) {
                        attributesArea.find(`.viwec-${section} .${currentKey}`).append(`<label class="viwec-group-name" for="input-model">${property.label}</label>`);
                    } else {
                        if (!property.hidden) {
                            let row = $(viWecTmpl('viwec-property', property));
                            row.find('.input').append(property.input);
                            if (typeof property.setup === 'function') row = property.setup(row, value); //Add custom events

                            attributesArea.find(`.viwec-${section} .${currentKey}`).append(row);
                            if (typeof property.inputType.subInit === 'function') {
                                property.inputType.subInit(element);
                            }
                        }
                    }

                    if (property.inputType.afterInit) {
                        property.inputType.afterInit(property.input);
                    }
                }
            }

            $('.vi-ui.accordion').accordion();

            if (component.init) component.init(ViWec.Builder.selectedEl.get(0));
        }
    };

    ViWec.Blocks = {
        _blocks: {},

        get(type) {
            return this._blocks[type];
        },

        add(type, data) {
            data.type = type;
            this._blocks[type] = data;
        },
    };

    ViWec.Builder = {
        component: {},
        dragMoveMutation: false,
        isPreview: false,
        designerMode: false,
        copyStorage: '',

        init(callback) {
            var self = this;

            self.selectedEl = null;
            self.initCallback = callback;
            self.dragElement = null;

            self.loadControlGroups();
            self.initDragDrop();
            self.initHandleBox();
            self.loadContentControl();
            self.loadBackgroundControl();
            self.initQuickAddLayout();
            self.globalEvent();
        },

        /* controls */
        loadControlGroups() {
            let componentsList = $("#viwec-components-list"), item = {};
            componentsList.empty();

            for (let group in ViWec.Components._categories) {
                componentsList.append(`<div class="vi-ui accordion styled fluid">
                                            <div class="title active">
                                                <i class="dropdown icon"> </i>
                                               ${ViWec.Components._categories[group].name}
                                            </div>
                                            <div class="content active" data-section="${group}">
                                                <ul></ul>
                                            </div>
                                        </div>`);

                let componentsSubList = componentsList.find('div[data-section="' + group + '"] ul');
                let components = ViWec.Components._categories[group].elements;

                if (!['layout', 'blocks'].includes(group)) group = 'content';

                for (let i in components) {
                    let componentType = components[i], controlBtn;
                    let component = ViWec.Components.get(componentType);

                    if (component) {
                        if (typeof component.setup === 'function') {
                            item = component.setup();
                            if (typeof component.onChange === 'function') component.onChange(item);
                        } else {
                            let classes = component.classes || '',
                                unLock = classes.includes('viwec-pro-version'),
                                dragAble = unLock ? '' : `viwec-${group}-draggable`,
                                unlockNotice = unLock ? "<div class='viwec-unlock-notice'><a href='#'>Unlock this feature</a></div>" : '',
                                lockIcon = unLock ? "<div class='dashicons dashicons-lock'></div>" : '',
                                info = component.info || '';

                            dragAble = componentType === 'blocks' ? 'viwec-blocks-draggable' : dragAble;

                            controlBtn = `<div class="viwec-control-btn ${dragAble} ${classes}" data-type="${componentType}" data-drag-type="component">
                                            ${lockIcon} ${unlockNotice} ${info}
                                            <div class="viwec-control-icon">
                                                <i class="viwec-ctrl-icon-${component.icon}"></i>
                                            </div>
                                            <div class="viwec-ctrl-title">${component.name}</div></div>`;

                            item = $(`<li  data-section="${group}">${controlBtn}</li>`);
                        }

                        componentsSubList.append(item);

                        if (group === 'layout') {
                            $('#viwec-quick-add-layout .viwec-layout-list').append(controlBtn);
                        }
                    }
                }
            }

            $('.vi-ui.accordion').accordion();
        },

        activeTab: (tab) => {
            $('#viwec-control-panel .item, #viwec-control-panel .tab').removeClass('active');
            $(`#viwec-control-panel [data-tab=${tab}]`).addClass('active');
        },

        clearTab: () => {
            $('#viwec-control-panel #viwec-attributes-list').empty();
        },

        loadLayoutControl(dataType) {
            this.clearTab();
            this.activeTab('editor');
            let type = dataType || this.selectedEl.data('type');
            ViWec.Components.render(type);
        },

        loadContentControl() {
            let self = this, body = $('#viwec-email-editor-wrapper');
            body.on('click', '.viwec-element', function (e) {
                self.removeFocus();
                $(this).addClass('viwec-element-focus');
                self.clearTab();
                self.activeTab('editor');
                let type = $(this).data('type');
                self.selectedEl = $(this);
                ViWec.Components.render(type);
            });
        },

        loadBackgroundControl() {
            let self = this;
            $('.viwec-edit-bgcolor-btn span').on('click', function (e) {
                self.clearTab();
                self.activeTab('editor');
                self.selectedEl = $('#viwec-email-editor-wrapper');
                ViWec.Components.render('background');
            });
        },

        initHandleBox() {
            let self = this, body = $('#viwec-email-editor-wrapper');

            body.on('click', '.viwec-delete-element-btn', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let $this = $(this);
                let thisColumn = $this.closest('.viwec-column');
                $this.closest('.viwec-element').remove();
                self.clearTab();
                self.activeTab('components');
                if (thisColumn.find('.viwec-element').length === 0) {
                    thisColumn.addClass('viwec-column-placeholder');
                }
            });

            body.on('click', '.viwec-duplicate-element-btn', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let currentEl = $(this).closest('.viwec-element');
                currentEl.after(currentEl.clone());
                self.removeFocus();
            });

            body.on('click', '.viwec-copy-element-btn', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let copiedEl = $(this).closest('.viwec-element');
                copiedEl = copiedEl.prop('outerHTML');
                localStorage.setItem('viwecCopy', copiedEl);
            });

            body.on('click', function (e) {
                if ($(e.target).is('.viwec-layout-row')) {
                    self.removeFocus();
                    self.selectedEl = $(e.target);
                    self.selectedEl.closest('.viwec-block').addClass('viwec-block-focus');
                    self.loadLayoutControl();
                }
            });

            body.on('click', '.viwec-template-block', function (e) {
                self.removeFocus();
                self.selectedEl = $(this);
                self.selectedEl.closest('.viwec-block').addClass('viwec-block-focus');
                self.loadLayoutControl();
            })
        },

        removeFocus() {
            $('body .viwec-element-focus').removeClass('viwec-element-focus');
            $('body .viwec-block-focus').removeClass('viwec-block-focus');
            this.clearTab();
            this.activeTab('components');
        },

        /* drag and drop */
        initDragDrop() {
            let self = this;
            let cursor = {cursor: 'move', cursorAt: {left: 40, top: 15}};

            $('.viwec-sortable').sortable({
                ...cursor,
                placeholder: 'viwec-placeholder',
                handle: '.dashicons-move',
                cancel: '',
                start(e, ui) {
                    ui.helper.addClass('viwec-is-dragging');
                },
                stop(ev, ui) {
                    ui.item.css({'width': 'auto', 'height': 'auto', 'z-index': 'unset'});
                    ui.item.find('.viwec-column-sortable').columnSortAble();
                    ui.item.removeClass('viwec-is-dragging');
                    viWecChange = true;
                }
            });

            $('.viwec-layout-draggable').draggable({
                ...cursor,
                helper() {
                    let type = $(this).data('type'), colsQty;

                    self.component = ViWec.Components.get(type);
                    colsQty = self.component.cols;

                    return viWecTmpl('viwec-block', {type: type, colsQty: colsQty});
                },
                start(e, ui) {
                    ui.helper.addClass('viwec-is-dragging');
                },
                stop(e, ui) {
                    ui.helper.handleRow();
                    ui.helper.find('.viwec-column').each(function (i, _this) {
                        $(_this).handleColumn();
                    });
                    ui.helper.removeClass('viwec-is-dragging');
                    viWecChange = true;
                },
                connectToSortable: viWecEditorArea
            });

            $('.viwec-content-draggable').draggable({
                ...cursor,
                helper() {
                    let $this = jQuery(this), html;

                    if ($this.data("drag-type") === "component") {
                        self.component = ViWec.Components.get($this.data("type"));
                    } else {
                        self.component = ViWec.Blocks.get($this.data("type"));
                    }

                    if (self.component.dragHtml) {
                        html = self.component.dragHtml;
                    } else {
                        html = self.component.html;
                    }

                    if ($(viWecEditorArea).children().length === 0) {
                        let row = $(viWecTmpl('viwec-block', {type: 'layout/grid1cols', colsQty: 1}));
                        row.handleRow().handleColumn();
                        row.find('.viwec-column-sortable').columnSortAble();
                        $('.viwec-sortable').append(row);
                    }

                    return `<div class='viwec-element' style="font-size:15px;border-radius: 0; overflow: hidden;line-height: 22px;" data-type="${$this.data('type')}">${html}</div>`;
                },
                start(ev, ui) {
                    ui.helper.addClass('viwec-is-dragging');
                },
                drag(ev, ui) {
                },
                stop(ev, ui) {
                    ui.helper.handleElement();
                    ui.helper.removeClass('viwec-is-dragging');
                    ui.helper.css('z-index', '');
                    ui.helper.trigger('click');
                    viWecChange = true;
                    $('#viwec-element-search input.viwec-search').val('').trigger('keyup');
                    $('#viwec-attributes-list input').trigger('keyup');
                },
                connectToSortable: '.viwec-column-sortable'
            });

            $('.viwec-blocks-draggable').draggable({
                ...cursor,
                helper() {
                    let blockHtml = 'No block was found', id = '';
                    if (viWecParams.templateBlocks.length) {
                        let {data} = viWecParams.templateBlocks[0];
                        id = viWecParams.templateBlocks[0].id;
                        blockHtml = ViWec.renderRows(data.rows, false);
                    }

                    let block = $(`<div class="viwec-block"><div class="viwec-template-block" data-type="blocks" data-block="${id}"></div></div>`);
                    block.find('.viwec-template-block').append(blockHtml);
                    return block;
                },
                start(e, ui) {
                    ui.helper.addClass('viwec-is-dragging');
                },
                stop(e, ui) {
                    ui.helper.handleRow();
                    ui.helper.removeClass('viwec-is-dragging');
                    // ui.helper.find('.viwec-edit-outer-row-btn').remove();
                    viWecChange = true;
                    ui.helper.find('.viwec-template-block').trigger('click');
                },
                connectToSortable: viWecEditorArea
            });
        },

        initQuickAddLayout() {
            $('#viwec-quick-add-layout .viwec-control-btn').on('click', function () {
                let type = $(this).data('type'), colsQty, row;
                self.component = ViWec.Components.get(type);
                colsQty = self.component.cols;
                row = $(viWecTmpl('viwec-block', {type: type, colsQty: colsQty}));
                row.handleRow().handleColumn();
                row.find('.viwec-column-sortable').columnSortAble();
                $('.viwec-sortable').append(row);
                $(this).closest('.viwec-layout-list').toggle();
            });
        },

        globalEvent() {
            let $this = this, body = $('body');

            body.on('click', function (e) {
                if ($(e.target).is('#wpwrap') || $(e.target).is('#viwec-email-editor-wrapper')) {
                    $this.removeFocus();
                    $('.viwec-layout-list').hide();
                }
            });
        }
    };

    ViWec.StyleManager = {

        setStyle(element, styleProp, value, unit) {
            return element.css(styleProp, value + unit);
        },

        _getCssStyle(element, property, key = null) {
            let styleProp = key ? key : property.key;

            if (styleProp === 'width' && property.unit && property.unit === '%') {
                let child = parseInt(element.css('width'));
                let parent = parseInt(element.parent().css('width'));
                if (parent > 0) {
                    return Math.round((child / parent) * 100) + '%';
                }
            } else {
                let el = element.get(0), css;
                if (el) {
                    if (el.style && el.style.length > 0 && el.style[styleProp])//check inline
                        css = el.style[styleProp];
                    else if (el.currentStyle)	//check defined css
                        css = el.currentStyle[styleProp];
                    else if (window.getComputedStyle) {
                        css = document.defaultView.getDefaultComputedStyle ?
                            document.defaultView.getDefaultComputedStyle(el, null).getPropertyValue(styleProp) :
                            window.getComputedStyle(el, null).getPropertyValue(styleProp);
                    }
                    if (css === 'transparent') css = '';
                    return css;
                }
            }
        },

        getStyle(element, property, key) {
            return this._getCssStyle(element, property, key);
        }
    };

});

