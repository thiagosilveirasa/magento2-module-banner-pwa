define([
    'Magento_Ui/js/form/element/ui-select',
    'jquery',
    'underscore'
], function (Select, $, _) {
    'use strict';

    return Select.extend({
        defaults: {
            validationUrl: false,
            loadedOption: [],
            validationLoading: true
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.validateInitialValue();

            return this;
        },

        /**
         * Validate initial value actually exists
         */
        validateInitialValue: function () {
            if (!_.isEmpty(this.value())) {
                $.ajax({
                    url: this.validationUrl,
                    type: 'GET',
                    dataType: 'json',
                    context: this,
                    data: {
                        productIds: this.value()
                    },

                    /** @param {Object} response */
                    success: function (response) {
                        if (!_.isEmpty(response)) {
                            this.options(response);
                            this.loadedOption = response;
                        }
                    },

                    /** set empty array if error occurs */
                    error: function () {
                        this.options([]);
                    },

                    /** stop loader */
                    complete: function () {
                        this.validationLoading(false);
                        this.setCaption();
                    }
                });
            } else {
                this.validationLoading(false);
            }
        },

        /** @inheritdoc */
        getSelected: function () {
            var options = this._super();

            if (!_.isEmpty(this.loadedOption)) {
                this.loadedOption = this.loadedOption.filter(product => this.value().includes(product.value));
                return this.loadedOption.length ? this.loadedOption : options;
            }

            return options;
        }
    });
});
