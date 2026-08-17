function cfte_renderForm(id) {
    var $iframe = jQuery('iframe[name="editor-canvas"]');
    var inIframe = $iframe.length > 0;
    
    // Search the iframe if we are in the editor, otherwise search the main document
    var $context = inIframe ? $iframe.contents() : jQuery(document);

    var $formStructure = jQuery("#form_structure" + id, $context);
    var $fbuilderDiv   = jQuery("#fbuilder_" + id, $context);

    if ($formStructure.length && $fbuilderDiv.length) {      
        
        var $tempContainer;
        var $structurePlaceholder;
        var $builderPlaceholder;

        if (inIframe) {
            // 1. Create invisible placeholders (namespaced with cfte_) to prevent conflicts
            $structurePlaceholder = jQuery('<div style="display:none;" id="cfte_placeholder_struct_' + id + '"></div>');
            $builderPlaceholder   = jQuery('<div style="display:none;" id="cfte_placeholder_build_' + id + '"></div>');
            
            $formStructure.before($structurePlaceholder);
            $fbuilderDiv.before($builderPlaceholder);

            // 2. Create a fake <form> in the main document. Namespaced to prevent conflict.
            $tempContainer = jQuery('<form id="cfte_temp_form_' + id + '" style="position:fixed; top:0; left:0; width:800px; height:800px; visibility:hidden; z-index:-9999;"></form>').appendTo('body');
            
            // Move elements to the main document
            $tempContainer.append($formStructure).append($fbuilderDiv);
        }

        try {
            // 3. Initialize the form builder using the specific CFTEfbuilder method[cite: 3]
            var cp_appbooking_fbuilder_myconfig = {"obj":"{\"pub\":true,\"identifier\":\"_"+id+"\",\"messages\": {}}"};
            var f = jQuery("#fbuilder_" + id).CFTEfbuilder(jQuery.parseJSON(cp_appbooking_fbuilder_myconfig.obj));
            f.fBuild.loadData("form_structure" + id);
        } catch(e) {
            console.error("CFTE Builder Error:", e);
        }

        if (inIframe) {
            // 4. Wait half a second to allow all asynchronous math/rendering to finish
            setTimeout(function() {
                // Move elements back to their exact original locations inside the iframe
                $structurePlaceholder.before($formStructure).remove();
                $builderPlaceholder.before($fbuilderDiv).remove();
                
                // Clean up our temporary form
                $tempContainer.remove();
            }, 500); 
        }

    } else {
        // If the ServerSideRender hasn't fetched the PHP HTML yet, wait and try again
        setTimeout(function() { 
            cfte_renderForm(id); 
        }, 100);
    }
}

jQuery(function() {             
    (function( blocks, element, blockEditor, components, serverSideRender ) {
        var el = element.createElement;
        var Fragment = element.Fragment;
        var useEffect = element.useEffect;
        
        // Handle backward compatibility just in case WP version is older
        var InspectorControls = blockEditor ? blockEditor.InspectorControls : window.wp.editor.InspectorControls;        
        var SelectControl = components.SelectControl;
        var PanelBody = components.PanelBody;
        var ServerSideRender = serverSideRender;

        const iconCPCFTE = el('img', { width: 20, height: 20, src:  "data:image/gif;base64,R0lGODlhFAAQAOYAAP//////AP8A//8AAAD//wD/AAAA/wAAAPH2+/T4/Ofw+Mne79Pk8t3q9d/r9ePu9wxrtQxstQ1stg1rtQ1stQ5ttg9ttg9uthBtthButhFuthhzuRl0uRx1uh11uh12uh52ux53ux93uyF4uyF5uyJ5uyN6vCR6vCd8vid7vSh9vSp+vi6AvzOEwTeFwjmHwz2JxECLxUCMxUSNxkSOxkWPxlOXy1SXy12czl6ezl+ezmCezmKfz2Kgz2Sh0Gai0Wai0Gei0Wej0Wij0Wum0mym02ym0nmt1nuv132w2H6x2ICx2ICy2IGz2YS02oa12oa22om424q424y53I+73Y663JG83pG83ZvC4ZrC4JzD4Z/F4qbJ5KjK5afJ5KjK5L3X68HZ7MTb7cne7uHt9uDs9evz+fL3+9Lk8eLu9uny+PD2+vj7/fP4+/b6/P///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAG8ALAAAAAAUABAAAAfhgACCg4SFhl84QUNDQoxCjY+MQztUAEpkPREQEZybnhCeNA5XGjBqWJqfoJucSAk3FSQRJgtiHxSaFJ8YXQ4uESEjoBlcZS66mhEUJ2NhHbogI8qgR2Y2uJovDlnYECAlERKrVgBJmzoAYMqcESCyFLg5DkxnWk1sT2hLuLjRrDxuYkRg0aBNDQgoFDgBRUGEsAhCEAgEtQEcPBUPpkCYEIIEBCFrZqiCgC3CijRSIniwAMSNDFDJSKbSlEJNlAtQ0rRAVhLeKlAcGGwB4MUHkSJIixgxohTp0iI/qhiaSjUQADs=" } );             
                
        /* Plugin Category */
        var categorySlug = 'cpcfte';
        var categoryExists = blocks.getCategories().some(function(cat) { return cat.slug === categorySlug; });
        if (!categoryExists) {
            blocks.getCategories().push({slug: categorySlug, title: 'Contact Form to Email'});
        }

        blocks.registerBlockType( 'cfte/form-rendering', {
            apiVersion: 3, 
            title: 'Contact Form to Email', 
            icon: iconCPCFTE,    
            category: 'cpcfte',
            supports: {
                customClassName: false,
                className: false
            },
            attributes: {
                formId: { type: 'string' },
                instanceId: { type: 'string' }
            },           
            edit: function( props ) {             
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                var isSelected = props.isSelected;
                var formOptions = typeof cfte_forms !== 'undefined' ? cfte_forms.forms : [];

                useEffect(function() {
                    if (!formOptions.length) return;
                    
                    var currentFormId = attributes.formId;
                    var currentInstanceId = attributes.instanceId;
                    var needsUpdate = false;

                    if (!currentInstanceId) {                        
                        currentInstanceId = formOptions[0].value + parseInt(Math.random() * 100000, 10);
                        needsUpdate = true;
                    }
                    if (!currentFormId) {
                        currentFormId = formOptions[0].value;
                        needsUpdate = true;
                    }

                    if (needsUpdate) {
                        setAttributes({ formId: currentFormId, instanceId: currentInstanceId });
                    }

                    if (currentInstanceId) {
                        cfte_renderForm(currentInstanceId);
                    }
                }, []); 

                if (!formOptions.length) {
                    return el("div", null, 'Please create a contact form first.');
                }
                                                       
                return el(
                    Fragment, 
                    null,
                    isSelected && el(
                        InspectorControls,
                        { key: 'cpcfte_inspector' },
                        el(
                            PanelBody,
                            { title: 'Help & Support' },
                            el('span', { style: { fontStyle: 'italic' } }, 'If you need help: '),
                            el('a', { href: 'https://form2email.dwbooster.com/contact-us', target: '_blank' }, 'CLICK HERE')
                        )
                    ),			    		
                    el(SelectControl, {
                        value: attributes.formId,
                        options: formOptions,
                        onChange: function(evt) {         
                            var newInstanceId = evt + parseInt(Math.random() * 100000, 10);
                            setAttributes({ formId: evt, instanceId: newInstanceId });
                            cfte_renderForm(newInstanceId);                                   
                        }
                    }),
                    el(ServerSideRender, {
                        block: "cfte/form-rendering",
                        attributes: attributes
                    })			    		
                );
            },
            save: function() {
                return null; 
            }
        });
    })(
        window.wp.blocks,
        window.wp.element,
        window.wp.blockEditor || window.wp.editor,
        window.wp.components,
        window.wp.serverSideRender
    );
});