VidLister.grid.Videos = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'vidlister-grid-videos'
        ,url: VidLister.config.connectorUrl
        ,baseParams: {
            action: 'mgr/video/getlist'
        }
        ,fields: ['id', 'active', 'source', 'videoId', 'name', 'description', 'keywords','topic', 'author', 'duration', 'jsondata']
        ,paging: true
        ,border: false
        ,frame: false
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 1
        },{
            header: _('vidlister.video.active')
            ,dataIndex: 'active'
            ,width: 1
            ,renderer: function(value) {
                return "<input disabled='disabled' type='checkbox'" + (value ? "checked='checked'" : "") + " />";
            }
        },{
            header: _('vidlister.video.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 10
        },{
            header: _('vidlister.video.source')
            ,dataIndex: 'source'
            ,sortable: true
            ,width: 4
        },{
            header: _('vidlister.video.topic')
            ,dataIndex: 'topic'
            ,sortable: true
            ,width: 4
        },{
			header: _('vidlister.video.author')
            ,dataIndex: 'author'
            ,sortable: true
            ,width: 4
        },{
            header: _('vidlister.video.duration')
            ,dataIndex: 'duration'
            ,sortable: true
            ,width: 2
        }]
        ,tbar: [{
                text: _('vidlister.import')
                ,handler: this.doImport
            },{
                text: _('vidlister.video.new')
                ,handler: this.addVideo
        }]
    });
    VidLister.grid.Videos.superclass.constructor.call(this,config)
};
Ext.extend(VidLister.grid.Videos,MODx.grid.Grid,{
    getMenu: function() {
        var m = [{
                text: _('vidlister.video.update')
                ,handler: this.updateVideo
            },{
                text: _('vidlister.video.remove')
                ,handler: this.removeVideo
            }
        ];
        this.addContextMenuItem(m);
        return true;
    }
    ,doImport: function(btn,e) {

        var console = MODx.load({
            xtype: 'modx-console'
            ,title: _('vidlister.import')
            ,register: 'mgr'
            ,topic: '/vidlisterimport/'
            ,show_filename: 0
            ,listeners: {
                'shutdown': {fn:function() {
                    window.location.reload();
                },scope:this}
           }
        });
        console.show(Ext.getBody());

        MODx.Ajax.request({
            url: VidLister.config.connectorUrl
            ,params: {
                action: 'mgr/video/import'
                ,register: 'mgr'
                ,topic: '/vidlisterimport/'
            }
            ,listeners: {
                'success':{fn:function() {
                    console.fireEvent('complete');
                },scope:this}
            }
        });

    }
    ,addVideo: function(btn,e) {
        this.VideoWindow = MODx.load({
            xtype: 'vidlister-window-video'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        this.VideoWindow.show(e.target);
        this.VideoWindow.setTitle(_('vidlister.video.new'));
        this.VideoWindow.reset();
    }
    ,updateVideo: function(btn,e) {
        this.VideoWindow = MODx.load({
            xtype: 'vidlister-window-video'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        this.VideoWindow.show(e.target);
        this.VideoWindow.setTitle(_('vidlister.video.update'));
        this.VideoWindow.setValues(this.menu.record);
    }
    ,removeVideo: function() {
        MODx.msg.confirm({
            title: _('vidlister.video.remove')
            ,text: _('vidlister.video.remove.confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/video/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('vidlister-grid-videos',VidLister.grid.Videos);

VidLister.window.Video = function(config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.applyIf(config,{
        title: _('vidlister.video.update')
        ,url: VidLister.config.connectorUrl
        ,autoHeight: true
        ,baseParams: {
            action: 'mgr/video/update'
        }
        ,width: 750
        ,closeAction: 'close'
        ,fields: [{
            xtype: 'modx-tabs'
            ,listeners: {
                'tabchange': function() {
                    this.syncSize();
                },
                scope: this
            }
            ,autoHeight: true
            ,deferredRender: false
            ,forceLayout: true
            ,width: '98%'
            ,borderStyle: 'padding: 10px 10px 10px 10px;'
            ,border: true
            ,defaults: {
                border: false
                ,labelWidth: 100
                ,autoHeight: true
                ,bodyStyle: 'padding: 5px 8px 5px 5px;'
                ,layout: 'form'
                ,deferredRender: false
                ,forceLayout: true
            }
            ,items: [{
                title: _('vidlister.video')
                ,items: [{
                        xtype: 'hidden'
                        ,name: 'id'
                    },{
                        xtype: 'xcheckbox'
                        ,fieldLabel: _('vidlister.video.active')
                        ,name: 'active'
                        ,inputValue: 1
                    },{
                        xtype: 'textfield'
                        ,fieldLabel: _('vidlister.video.id')
                        ,name: 'videoId'
                        ,width: '98%'
                        ,allowBlank: false
                    },{
					xtype: 'modx-combo'
                        ,fieldLabel: _('vidlister.video.topic')
                        ,name: 'topic'
                        ,width: '100%'
						,anchor:'100%'	// ширина элемента в окне
                        ,url: VidLister.config.connectorUrl
                        ,fields: ['id','topic']
                        ,displayField: 'topic'
							,valueField: 'id'
							,hiddenName : 'topic' // название поля в которое будет опроавленно значение valueField
							// если оно не равно значению displayField, то значение выпадающего списка при первом открытии будет пустовать до тех пор пока не тычнем на него
							// ,hiddenValue: 'source2'	// если ниче не выбрали то по-умолчанию отправляется с полем hiddenName это значение
                        ,baseParams: { action: 'mgr/video/gettopic' }
                        ,allowBlank: true	// значит: можно оставлять пустым? (да,нет)
						// ,blankText: '5555444'
						,emptyText: 'не выбрана' //надпись в поле если ничего не указано
                    },{
					xtype: 'modx-combo'
                        ,fieldLabel: _('vidlister.video.source')
                        ,name: 'source'
                        ,width: '100%'
						,anchor:'100%'	// ширина элемента в окне
                        ,url: VidLister.config.connectorUrl
                        ,fields: ['source']
                        ,displayField: 'source'
                        ,valueField: 'source'
                        ,baseParams: { action: 'mgr/video/getsources' }
                        ,allowBlank: false
						,editable: true
						,forceSelection: false
						,typeAhead: true // you can also input text, not only choise from list
                    },{
                        xtype: 'textfield'
                        ,fieldLabel: _('vidlister.video.author')
                        ,name: 'author'
                        ,width: '98%'
                        ,allowBlank: false
                    },{
                        xtype: 'numberfield'
                        ,fieldLabel: _('vidlister.video.duration')
                        ,name: 'duration'
                        ,width: '98%'
                        ,allowBlank: false
                    },{
                        xtype: 'textfield'
                        ,fieldLabel: _('vidlister.video.name')
                        ,name: 'name'
                        ,width: '98%'
                        ,allowBlank: false
                    },{
                        xtype: 'textfield'
                        ,fieldLabel: _('vidlister.video.keywords')
                        ,name: 'keywords'
                        ,width: '98%'
                        ,allowBlank: true
                    },{
                        xtype: 'textarea'
                        ,fieldLabel: _('vidlister.video.description')
                        ,name: 'description'
                        ,width: '98%'
                        ,height: 200
                        ,allowBlank: true
                    }
                ]
            },{
                title: _('vidlister.video.advanced')
                ,items: [{
                    xtype: 'textarea'
                    ,fieldLabel: _('vidlister.video.jsondata')
                    ,name: 'jsondata'
                    ,width: '98%'
                    ,height: 300
                    ,allowBlank: true
                }]
            }]
        }]
    });
    VidLister.window.Video.superclass.constructor.call(this,config);
};
Ext.extend(VidLister.window.Video,MODx.Window);
Ext.reg('vidlister-window-video',VidLister.window.Video);