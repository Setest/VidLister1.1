
<div class="tabbable tabs-left">
        <ul class="nav nav-tabs">
            <li class="nav-header">[[%list]]</li>
    		[[Wayfinder?
				&startId=`[[++vidlister.video.topic.parent]]`
                &titleOfLinks  = `id`
				&level=`2`
                &ignoreHidden=`1`
				&outerTpl=`vlWayfinder.tpl.row.outer`
				&parentRowTpl=`vlWayfinder.tpl.row.parent`
				&innerRowTpl=`vlWayfinder.tpl.row.inner`
				&rowTpl=`vlWayfinder.tpl.row`
			]]
		</ul>

        

    <div class="tab-content">
        <div class="tab-pane active">
            <div id="gridArea">
        
                <ul id="tiles">
                    [[!getPage?
                      &element=`VidLister`
                      &pageVarKey=`page`
                      &limit=`20`
                      &pageActiveTpl=`<li[[+activeClasses:default=` class="active"`]]><a[[+activeClasses:default=` class="active"`]][[+title]] href="[[+href]]">[[+pageNo]]</a></li>`
                      &pageFirstTpl=`<li class="control"><a[[+classes]][[+title]] href="[[+href]]">Первая</a></li>`
                      &pageLastTpl=`<li class="control"><a[[+classes]][[+title]] href="[[+href]]">Последняя</a></li>`
                      &tpl=`{"youtube":"vlYoutubeSetest","vimeo":"vlVimeo"}`
                    ]]
                </ul>
            </div>
            
        <div class="pagination pagination-centered">
        <ul>
          [[!+page.nav]]
        </ul>
        </div>
    </div>
    </div>
</div>