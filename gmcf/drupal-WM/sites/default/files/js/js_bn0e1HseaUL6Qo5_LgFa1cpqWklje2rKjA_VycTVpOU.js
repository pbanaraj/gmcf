window.matchMedia||(window.matchMedia=function(){"use strict";var e=window.styleMedia||window.media;if(!e){var t=document.createElement("style"),i=document.getElementsByTagName("script")[0],n=null;t.type="text/css";t.id="matchmediajs-test";i.parentNode.insertBefore(t,i);n="getComputedStyle"in window&&window.getComputedStyle(t,null)||t.currentStyle;e={matchMedium:function(e){var i="@media "+e+"{ #matchmediajs-test { width: 1px; } }";if(t.styleSheet){t.styleSheet.cssText=i}else{t.textContent=i}return n.width==="1px"}}}return function(t){return{matches:e.matchMedium(t||"all"),media:t||"all"}}}());
;
/**
 * @file
 * CKEditor implementation of {@link Drupal.editors} API.
 */

(function (Drupal, debounce, CKEDITOR, $) {

  'use strict';

  /**
   * @namespace
   */
  Drupal.editors.ckeditor = {

    /**
     * Editor attach callback.
     *
     * @param {HTMLElement} element
     *   The element to attach the editor to.
     * @param {string} format
     *   The text format for the editor.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.replace()` created an editor or not.
     */
    attach: function (element, format) {
      this._loadExternalPlugins(format);
      // Also pass settings that are Drupal-specific.
      format.editorSettings.drupal = {
        format: format.format
      };

      // Set a title on the CKEditor instance that includes the text field's
      // label so that screen readers say something that is understandable
      // for end users.
      var label = $('label[for=' + element.getAttribute('id') + ']').html();
      format.editorSettings.title = Drupal.t('Rich Text Editor, !label field', {'!label': label});

      return !!CKEDITOR.replace(element, format.editorSettings);
    },

    /**
     * Editor detach callback.
     *
     * @param {HTMLElement} element
     *   The element to detach the editor from.
     * @param {string} format
     *   The text format used for the editor.
     * @param {string} trigger
     *   The event trigger for the detach.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.dom.element.get(element).getEditor()`
     *   found an editor or not.
     */
    detach: function (element, format, trigger) {
      var editor = CKEDITOR.dom.element.get(element).getEditor();
      if (editor) {
        if (trigger === 'serialize') {
          editor.updateElement();
        }
        else {
          editor.destroy();
          element.removeAttribute('contentEditable');
        }
      }
      return !!editor;
    },

    /**
     * Reacts on a change in the editor element.
     *
     * @param {HTMLElement} element
     *   The element where the change occured.
     * @param {function} callback
     *   Callback called with the value of the editor.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.dom.element.get(element).getEditor()`
     *   found an editor or not.
     */
    onChange: function (element, callback) {
      var editor = CKEDITOR.dom.element.get(element).getEditor();
      if (editor) {
        editor.on('change', debounce(function () {
          callback(editor.getData());
        }, 400));
      }
      return !!editor;
    },

    /**
     * Attaches an inline editor to a DOM element.
     *
     * @param {HTMLElement} element
     *   The element to attach the editor to.
     * @param {object} format
     *   The text format used in the editor.
     * @param {string} [mainToolbarId]
     *   The id attribute for the main editor toolbar, if any.
     * @param {string} [floatedToolbarId]
     *   The id attribute for the floated editor toolbar, if any.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.replace()` created an editor or not.
     */
    attachInlineEditor: function (element, format, mainToolbarId, floatedToolbarId) {
      this._loadExternalPlugins(format);
      // Also pass settings that are Drupal-specific.
      format.editorSettings.drupal = {
        format: format.format
      };

      var settings = $.extend(true, {}, format.editorSettings);

      // If a toolbar is already provided for "true WYSIWYG" (in-place editing),
      // then use that toolbar instead: override the default settings to render
      // CKEditor UI's top toolbar into mainToolbar, and don't render the bottom
      // toolbar at all. (CKEditor doesn't need a floated toolbar.)
      if (mainToolbarId) {
        var settingsOverride = {
          extraPlugins: 'sharedspace',
          removePlugins: 'floatingspace,elementspath',
          sharedSpaces: {
            top: mainToolbarId
          }
        };

        // Find the "Source" button, if any, and replace it with "Sourcedialog".
        // (The 'sourcearea' plugin only works in CKEditor's iframe mode.)
        var sourceButtonFound = false;
        for (var i = 0; !sourceButtonFound && i < settings.toolbar.length; i++) {
          if (settings.toolbar[i] !== '/') {
            for (var j = 0; !sourceButtonFound && j < settings.toolbar[i].items.length; j++) {
              if (settings.toolbar[i].items[j] === 'Source') {
                sourceButtonFound = true;
                // Swap sourcearea's "Source" button for sourcedialog's.
                settings.toolbar[i].items[j] = 'Sourcedialog';
                settingsOverride.extraPlugins += ',sourcedialog';
                settingsOverride.removePlugins += ',sourcearea';
              }
            }
          }
        }

        settings.extraPlugins += ',' + settingsOverride.extraPlugins;
        settings.removePlugins += ',' + settingsOverride.removePlugins;
        settings.sharedSpaces = settingsOverride.sharedSpaces;
      }

      // CKEditor requires an element to already have the contentEditable
      // attribute set to "true", otherwise it won't attach an inline editor.
      element.setAttribute('contentEditable', 'true');

      return !!CKEDITOR.inline(element, settings);
    },

    /**
     * Loads the required external plugins for the editor.
     *
     * @param {object} format
     *   The text format used in the editor.
     */
    _loadExternalPlugins: function (format) {
      var externalPlugins = format.editorSettings.drupalExternalPlugins;
      // Register and load additional CKEditor plugins as necessary.
      if (externalPlugins) {
        for (var pluginName in externalPlugins) {
          if (externalPlugins.hasOwnProperty(pluginName)) {
            CKEDITOR.plugins.addExternal(pluginName, externalPlugins[pluginName], '');
          }
        }
        delete format.editorSettings.drupalExternalPlugins;
      }
    }

  };

  Drupal.ckeditor = {

    /**
     * Variable storing the current dialog's save callback.
     *
     * @type {?function}
     */
    saveCallback: null,

    /**
     * Open a dialog for a Drupal-based plugin.
     *
     * This dynamically loads jQuery UI (if necessary) using the Drupal AJAX
     * framework, then opens a dialog at the specified Drupal path.
     *
     * @param {CKEditor} editor
     *   The CKEditor instance that is opening the dialog.
     * @param {string} url
     *   The URL that contains the contents of the dialog.
     * @param {object} existingValues
     *   Existing values that will be sent via POST to the url for the dialog
     *   contents.
     * @param {function} saveCallback
     *   A function to be called upon saving the dialog.
     * @param {object} dialogSettings
     *   An object containing settings to be passed to the jQuery UI.
     */
    openDialog: function (editor, url, existingValues, saveCallback, dialogSettings) {
      // Locate a suitable place to display our loading indicator.
      var $target = $(editor.container.$);
      if (editor.elementMode === CKEDITOR.ELEMENT_MODE_REPLACE) {
        $target = $target.find('.cke_contents');
      }

      // Remove any previous loading indicator.
      $target.css('position', 'relative').find('.ckeditor-dialog-loading').remove();

      // Add a consistent dialog class.
      var classes = dialogSettings.dialogClass ? dialogSettings.dialogClass.split(' ') : [];
      classes.push('ui-dialog--narrow');
      dialogSettings.dialogClass = classes.join(' ');
      dialogSettings.autoResize = window.matchMedia('(min-width: 600px)').matches;
      dialogSettings.width = 'auto';

      // Add a "Loading…" message, hide it underneath the CKEditor toolbar,
      // create a Drupal.Ajax instance to load the dialog and trigger it.
      var $content = $('<div class="ckeditor-dialog-loading"><span style="top: -40px;" class="ckeditor-dialog-loading-link">' + Drupal.t('Loading...') + '</span></div>');
      $content.appendTo($target);

      var ckeditorAjaxDialog = Drupal.ajax({
        dialog: dialogSettings,
        dialogType: 'modal',
        selector: '.ckeditor-dialog-loading-link',
        url: url,
        progress: {type: 'throbber'},
        submit: {
          editor_object: existingValues
        }
      });
      ckeditorAjaxDialog.execute();

      // After a short delay, show "Loading…" message.
      window.setTimeout(function () {
        $content.find('span').animate({top: '0px'});
      }, 1000);

      // Store the save callback to be executed when this dialog is closed.
      Drupal.ckeditor.saveCallback = saveCallback;
    }
  };

  // Moves the dialog to the top of the CKEDITOR stack.
  $(window).on('dialogcreate', function (e, dialog, $element, settings) {
    $('.ui-dialog--narrow').css('zIndex', CKEDITOR.config.baseFloatZIndex + 1);
  });

  // Respond to new dialogs that are opened by CKEditor, closing the AJAX loader.
  $(window).on('dialog:beforecreate', function (e, dialog, $element, settings) {
    $('.ckeditor-dialog-loading').animate({top: '-40px'}, function () {
      $(this).remove();
    });
  });

  // Respond to dialogs that are saved, sending data back to CKEditor.
  $(window).on('editor:dialogsave', function (e, values) {
    if (Drupal.ckeditor.saveCallback) {
      Drupal.ckeditor.saveCallback(values);
    }
  });

  // Respond to dialogs that are closed, removing the current save handler.
  $(window).on('dialog:afterclose', function (e, dialog, $element) {
    if (Drupal.ckeditor.saveCallback) {
      Drupal.ckeditor.saveCallback = null;
    }
  });

  // Set the CKEditor cache-busting string to the same value as Drupal.
  CKEDITOR.timestamp = drupalSettings.ckeditor.timestamp;

})(Drupal, Drupal.debounce, CKEDITOR, jQuery);
;
/**
 * @file
 * Text behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Auto-hide summary textarea if empty and show hide and unhide links.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches auto-hide behavior on `text-summary` events.
   */
  Drupal.behaviors.textSummary = {
    attach: function (context, settings) {
      $(context).find('.js-text-summary').once('text-summary').each(function () {
        var $widget = $(this).closest('.js-text-format-wrapper');

        var $summary = $widget.find('.js-text-summary-wrapper');
        var $summaryLabel = $summary.find('label').eq(0);
        var $full = $widget.find('.js-text-full').closest('.js-form-item');
        var $fullLabel = $full.find('label').eq(0);

        // Create a placeholder label when the field cardinality is greater
        // than 1.
        if ($fullLabel.length === 0) {
          $fullLabel = $('<label></label>').prependTo($full);
        }

        // Set up the edit/hide summary link.
        var $link = $('<span class="field-edit-link"> (<button type="button" class="link link-edit-summary">' + Drupal.t('Hide summary') + '</button>)</span>');
        var $button = $link.find('button');
        var toggleClick = true;
        $link.on('click', function (e) {
          if (toggleClick) {
            $summary.hide();
            $button.html(Drupal.t('Edit summary'));
            $link.appendTo($fullLabel);
          }
          else {
            $summary.show();
            $button.html(Drupal.t('Hide summary'));
            $link.appendTo($summaryLabel);
          }
          e.preventDefault();
          toggleClick = !toggleClick;
        }).appendTo($summaryLabel);

        // If no summary is set, hide the summary field.
        if ($widget.find('.js-text-summary').val() === '') {
          $link.trigger('click');
        }
      });
    }
  };

})(jQuery, Drupal);
;
(function($) {

  // Make evrything local
  var ml = ml || {};
  ml.options = ml.options || {};

  Drupal.behaviors.maxlength = {
    attach: function(context) {
      var $context = $(context);

      if (Drupal.ckeditor != undefined) {
        ml.ckeditor();
      }

      $context.find('.maxlength').once('maxlength').each(function () {
        var options = {};
        var $this = $(this);
        options['counterText'] = $this.attr('maxlength_js_label');
        if ($this.hasClass('maxlength_js_enforce')) {
          options['enforce'] = true;
        }
        if ($this.hasClass('maxlength_js_truncate_html')) {
          options['truncateHtml'] = true;
        }
        $this.charCount(options);
      });
    },
    detach: function(context, settings) {
      var $context = $(context);
      $context.find('.maxlength').removeOnce('maxlength').each(function () {
        $(this).charCount({
          action: 'detach'
        });
      });
    }
  };

  /**
   * Code below is based on:
   *   Character Count Plugin - jQuery plugin
   *   Dynamic character count for text areas and input fields
   *   written by Alen Grakalic
   *   http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
   *
   *  @param obj
   *    a jQuery object for input elements
   *  @param options
   *    an array of options.
   *  @param count
   *    In case obj.val() wouldn't return the text to count, this should
   *    be passed with the number of characters.
   */
  ml.calculate = function(obj, options, count, wysiwyg, getter, setter) {
    var counter = $('#' + obj.attr('id') + '-' + options.css);
    var limit = parseInt(obj.attr('maxlength'));

    if (count == undefined) {
      if (options.truncateHtml) {
        count = ml.strip_tags(obj.val()).length;
      }
      else {
        count = ml.twochar_lineending(obj.val()).length;
      }
    }

    var available = limit - count;

    if (available <= options.warning) {
      counter.addClass(options.cssWarning);
    }
    else {
      counter.removeClass(options.cssWarning);
    }

    if (available < 0) {
      counter.addClass(options.cssExceeded);
      // Trim text.
      if (options.enforce) {
        if (wysiwyg != undefined) {
          if (typeof ml[getter] == 'function' && typeof ml[setter] == 'function') {
              if (options.truncateHtml) {
                var new_html = ml.truncate_html(ml[getter](wysiwyg), limit)
                ml[setter](wysiwyg, new_html);
                count = ml.strip_tags(new_html).length;
              } else {
                var new_html = ml[getter](wysiwyg).substr(0, limit);
                ml[setter](wysiwyg, new_html);
                count = new_html.length;
              }
          }
        } else {
          if (options.truncateHtml) {
            obj.val(ml.truncate_html(obj.val(), limit));
            // Re calculate text length
            count = ml.strip_tags(obj.val()).length;
          } else {
            obj.val(obj.val().substr(0, limit));
            // Re calculate text length
            count = ml.twochar_lineending(obj.val()).length;
          }
        }
      }
      available = limit - count;
    }
    else {
      counter.removeClass(options.cssExceeded);
    }

    counter.html(options.counterText.replace('@limit', limit).replace('@remaining', available).replace('@count', count));
  };

  /**
   * Replaces line ending with to chars, because PHP-calculation counts with two chars
   * as two characters.
   *
   * @see http://www.sitepoint.com/blogs/2004/02/16/line-endings-in-javascript/
   */
  ml.twochar_lineending = function(str) {
    return str.replace(/(\r\n|\r|\n)/g, "\r\n");
  };

  ml.strip_tags = function(input, allowed) {
    // making the lineendings with two chars
    input = ml.twochar_lineending(input);
    // We do want that the space characters to count as 1, not 6...
    input = input.replace('&nbsp;', ' ');
    //input = input.split(' ').join('');
    // Strips HTML and PHP tags from a string
    allowed = (((allowed || "") + "")
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1){
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
  };

  /**
   * Cuts a html text up to limit characters. Still experimental.
   */
  ml.truncate_html = function(text, limit) {
    // The html result after cut.
    var result_html = '';
    // The text result, that will actually used when counting characters.
    var result_text = '';
    // A stack that will keep the tags that are open at a given time.
    var tags_open = new Array();
    // making the lineendings with two chars
    text = ml.twochar_lineending(text);
    // We do want that the space characters to count as 1, not 6...
    text = text.replace('&nbsp;', ' ');
    while (result_text.length < limit && text.length > 0) {
      switch (text.charAt(0)) {
        case '<': {
          if (text.charAt(1) != '/') {
            var tag_name = '';
            var tag_name_completed = false;
            while (text.charAt(0) != '>' && text.length > 0) {
              var first_char = text.charAt(0).toString();
              // Until the tag is closed, we do not append anything
              // to the visible text, only to the html.
              result_html += first_char;
              // Also, check if we have a valid tag name.
              if (!tag_name_completed && first_char == ' ') {
                // We have the tag name, so push it into the stack.
                tag_name_completed = true;
                tags_open.push(tag_name);
              }
              // Check if we are still in the tag name.
              if (!tag_name_completed && first_char != '<') {
                tag_name += first_char;
              }
              //If we have the combination "/>" it means that the tag
              //is closed, so remove it from the open tags stack.
              if (first_char == '/' && text.length > 1 && text.charAt(1) == '>') {
                tags_open.pop();
              }
              //Done with this char, remove it from the original text.
              text = text.substring(1);
            }
            if (!tag_name_completed) {
              // In this case we have a tag like "<strong>some text</strong> so
              // we did not have any attributes in the tag, but still, the tag
              // has to be marked as open.
              tags_open.push(tag_name);
            }
            //We are here, then the tag is closed, so just remove the
            //remaining ">" character.
            if (text.length > 0) {
              result_html += text.charAt(0).toString();
            }
          } else {
            // In this case, we have an ending tag.
            // The name of the ending tag should match the last open tag,
            // otherwise, something is wrong with th html text.
            var tag_name = '';
            while (text.charAt(0) != '>' && text.length > 0) {
              var first_char = text.charAt(0).toString();
              if (first_char != '<' && first_char != '/') {
                tag_name += first_char;
              }
              result_html += first_char;
              text = text.substring(1);
            }
            if (text.length > 0) {
              result_html = result_html + text.charAt(0).toString();
            }
            // Pop the last element from the tags stack and compare it with
            // the tag name.
            var expected_tag_name = tags_open.pop();
            if (expected_tag_name != tag_name) {
              //Should throw an exception, but for the moment just alert.
              alert('Expected end tag: ' + expected_tag_name + '; Found end tag: '+tag_name);
            }
          }
          break;
        }
        default: {
          // In this case, we have a character that should also count for the
          // limit, so append it to both, the html and text result.
          var first_char = text.charAt(0).toString();
          result_html += first_char;
          result_text += first_char;
          break;
        }
      }
      // Remove the first character, it did its job.
      text = text.substring(1);
    }
    // Restore the open tags that were not closed. This happens when the text
    // got truncated in the middle of one or more html tags.
    var tag = '';
    while (tag = tags_open.pop()) {
      result_html += '</' + tag + ">";
    }
    return result_html;
  }

  $.fn.charCount = function(options) {
    // default configuration properties
    var defaults = {
      warning: 10,
      css: 'counter',
      counterElement: 'div',
      cssWarning: 'warning',
      cssExceeded: 'error',
      counterText: Drupal.t('Content limited to @limit characters, remaining: <strong>@remaining</strong>'),
      action: 'attach',
      enforce: false,
      truncateHtml: false
    };

    var options = $.extend(defaults, options);
    ml.options[$(this).attr('id')] = options;

    if (options.action == 'detach') {
      $(this).removeOnce('maxlength');
      $('#' + $(this).attr('id') + '-' + options.css).remove();
      delete ml.options[$(this).attr('id')];
      return 'removed';
    }

    var counterElement = $('<' + options.counterElement + ' id="' + $(this).attr('id') + '-' + options.css + '" class="' + options.css + '"></' + options.counterElement + '>');
    if ($(this).next('div.grippie').length) {
      $(this).next('div.grippie').after(counterElement);
    } else {
      $(this).after(counterElement);
    }

    ml.calculate($(this), options);
    $(this).keyup(function() {
      ml.calculate($(this), options);
    });
    $(this).change(function() {
      ml.calculate($(this), options);
    });

  };

  /**
   * Integrate with ckEditor
   * Detect changes on editors and invoke ml.calculate()
   */
  ml.ckeditor = function() {
    // We only run it once
    var onlyOnce = false;
    if (!onlyOnce) {
      onlyOnce = true;
      CKEDITOR.on('instanceReady', function(e) {
        var editor = $('#' + e.editor.name + '.maxlength');
        if (editor.length == 1) {
          if (editor.hasClass('maxlength_js_enforce')) {
            ml.options[e.editor.element.getId()].enforce = true;
          } else {
            ml.options[e.editor.element.getId()].enforce = false;
          }
          // Check if we should strip the tags when counting.
          if (editor.hasClass('maxlength_js_truncate_html')) {
            ml.options[e.editor.element.getId()].truncateHtml = true;
          } else {
            ml.options[e.editor.element.getId()].truncateHtml = false;
          }
          // Add the events on the editor.
          e.editor.on('key', function(e) {
            setTimeout(function(){ml.ckeditorChange(e)}, 100);
          });
          e.editor.on('paste', function(e) {
            setTimeout(function(){ml.ckeditorChange(e)}, 500);
          });
          e.editor.on('elementsPathUpdate', function(e) {
            setTimeout(function(){ml.ckeditorChange(e)}, 100);
          });
        }
      });
    }
  }
  // Invoke ml.calculate() for editor
  ml.ckeditorChange = function(e) {
    // Clone to avoid changing defaults
    var options = $.extend({}, ml.options[e.editor.element.getId()]);
    if (options.truncateHtml){
      ml.calculate($('#' + e.editor.element.getId()), options, ml.strip_tags(ml.ckeditorGetData(e)).length, e, 'ckeditorGetData', 'ckeditorSetData');
    }
    else {
      ml.calculate($('#' + e.editor.element.getId()), options, ml.twochar_lineending(ml.ckeditorGetData(e)).length, e, 'ckeditorGetData', 'ckeditorSetData');
    }
  };

  // Gets the data from the ckeditor.
  ml.ckeditorGetData = function(e) {
    return e.editor.getData();
  }

  // Sets the data into a ckeditor.
  ml.ckeditorSetData = function(e, data) {
    e.editor.setData(data);
  }

})(jQuery);
;
