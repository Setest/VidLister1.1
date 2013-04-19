<li class="[[+wf.classnames]] [[!If?
       &subject=`[[!getParamFromUrl? &field=`topic`]]`
       &operator=`EQ`
       &operand=`[[+wf.docid]]`
       &then=`active`
    ]] [[!If?
       &subject=`[[!getParamFromUrl? &field=`topic`]]`
       &operator=`isempty`
       &then=`[[+wf.menuindex:is=`0`:then=`active`]]`
	]]
">
	
	<a href="[[~[[*id]]?]]&topic=[[+wf.menuindex:is=`0`:then=`0`:else=`[[+wf.docid]]`]]" [[+wf.attributes]]>[[+wf.linktext]]</a>
	[[+wf.wrapper]]
</li>