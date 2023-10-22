define([
  "Magento_PageBuilder/js/content-type/preview",
  "Magento_PageBuilder/js/uploader"
], function(
  PreviewBase,
  Uploader
) {
  "use strict";

  /**
   * Quote content type preview class
   *
   * @param parent
   * @param config
   * @param stageId
   * @constructor
   */
  function Preview(parent, config, stageId) {
    PreviewBase.call(this, parent, config, stageId);
  }

  var $super = PreviewBase.prototype;

  Preview.prototype = Object.create(PreviewBase.prototype);

  /**
   * Return a new instance of the uploader to allow for inline image uploading capabilities
   *
   * @returns {*}
   */
  Preview.prototype.getUploader = function() {
    var initialImageValue = this.contentType.dataStore.get(
      this.config.additional_data.uploaderConfig.dataScope,
      ""
    );

    return new Uploader(
      "imageuploader_" + this.contentType.id,
      this.config.additional_data.uploaderConfig,
      this.contentType.id,
      this.contentType.dataStore,
      initialImageValue
    );
  };

  /**
   * Modify the options returned by the content type
   *
   * @returns {*}
   */
  Preview.prototype.retrieveOptions = function() {
    var options = $super.retrieveOptions.call(this, arguments);

    // options.hideShow = new hideShowOption({
    //   preview: this,
    //   icon: hideShowOption.showIcon,
    //   title: hideShowOption.showText,
    //   action: this.onOptionVisibilityToggle,
    //   classes: ["hide-show-content-type"],
    //   sort: 40
    // });
    // return options;

    // Change option menu icons
    options.remove.icon = "<i class='icon-admin-pagebuilder-error'></i>";

    // Change tooltips
    options.edit.title = "Open Editor";
    options.remove.title = "Remove";
    // options.move.title = "Move";
    // options.duplicate.title = "Duplicate";

    // Remove menu options
    // delete options.move;
    // delete options.duplicate;
    // delete options.edit;
    // delete options.remove;

    return options;
  };

  return Preview;
});
