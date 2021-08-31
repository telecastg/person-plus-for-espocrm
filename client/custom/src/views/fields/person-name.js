define('custom:views/fields/person-name', 'views/fields/person-name', function (Dep) {

    return Dep.extend({

        editTemplateFirstMiddleLastSuffix: 'custom:fields/person-name/edit-first-middle-last-suffix',
        editTemplateFirstMiddleLastMother: 'custom:fields/person-name/edit-first-middle-last-mother',
        editTemplateFirstMiddleLastMotherSuffix: 'custom:fields/person-name/edit-first-middle-last-mother-suffix',

        data: function () {
            var data = Dep.prototype.data.call(this);
            data.ucName = Espo.Utils.upperCaseFirst(this.name);
            data.salutationValue = this.model.get(this.salutationField);
            data.firstValue = this.model.get(this.firstField);
            data.lastValue = this.model.get(this.lastField);
            data.middleValue = this.model.get(this.middleField);
            data.motherValue = this.model.get(this.motherField);
            data.suffixValue = this.model.get(this.suffixField);

            data.salutationOptions = this.model.getFieldParam(this.salutationField, 'options');
            data.suffixOptions = this.model.getFieldParam(this.suffixField, 'options');


            if (this.mode === 'edit') {
                data.firstMaxLength = this.model.getFieldParam(this.firstField, 'maxLength');
                data.lastMaxLength = this.model.getFieldParam(this.lastField, 'maxLength');
                data.middleMaxLength = this.model.getFieldParam(this.middleField, 'maxLength');
                data.motherMaxLength = this.model.getFieldParam(this.motherField, 'maxLength');
            }

            data.valueIsSet = this.model.has(this.firstField) || this.model.has(this.lastField);

            if (this.mode === 'detail') {
                data.isNotEmpty = !!data.firstValue || !!data.lastValue || !!data.salutationValue || !!data.middleValue || !!data.motherValue || !!data.suffixValue;
            } else if (this.mode === 'list' || this.mode === 'listLink') {
                data.isNotEmpty = !!data.firstValue || !!data.lastValue || !!data.middleValue || !!data.motherValue || !!data.suffixValue;
            }

            if (data.isNotEmpty && this.mode == 'detail' || this.mode == 'list' || this.mode === 'listLink') {
                data.formattedValue = this.getFormattedValue();
            }

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);
            var ucName = Espo.Utils.upperCaseFirst(this.name);
            this.salutationField = 'salutation' + ucName;
            this.firstField = 'first' + ucName;
            this.lastField = 'last' + ucName;
            this.middleField = 'middle' + ucName;
            this.motherField = 'mother' + ucName;
            this.suffixField = 'suffix' + ucName;
        },

        afterRender: function () {
            console.log('custom:views/fields/person-name.js afterRender() this = ', this);
            Dep.prototype.afterRender.call(this);
            if (this.mode == 'edit') {
                this.$salutation = this.$el.find('[data-name="' + this.salutationField + '"]');
                this.$first = this.$el.find('[data-name="' + this.firstField + '"]');
                this.$last = this.$el.find('[data-name="' + this.lastField + '"]');

                if (this.formatHasMiddle()) {
                    this.$middle = this.$el.find('[data-name="' + this.middleField + '"]');
                }
                
                if (this.formatHasMother()) {
                    this.$mother = this.$el.find('[data-name="' + this.motherField + '"]');
                }

                if (this.formatHasSuffix()) {
                    this.$suffix = this.$el.find('[data-name="' + this.suffixField + '"]');
                }
                
                this.$salutation.on('change', function () {
                    this.trigger('change');
                }.bind(this));
                this.$first.on('change', function () {
                    this.trigger('change');
                }.bind(this));
                this.$last.on('change', function () {
                    this.trigger('change');
                }.bind(this));
            }
        },

        getFormattedValue: function () {
            var salutation = this.model.get(this.salutationField);
            var first = this.model.get(this.firstField);
            var last = this.model.get(this.lastField);
            var middle = this.model.get(this.middleField);
            var mother = this.model.get(this.motherField);
            var suffix = this.model.get(this.suffixField);

            if (salutation) {
                salutation = this.getLanguage().translateOption(salutation, 'salutationName', this.model.entityType);
            }

            if(suffix) {
                suffix = this.getLanguage().translateOption(suffix, 'suffixName', this.model.entityType);
            }

            var value = '';

            var format = this.getFormat();
            
            var arr = [];

            switch (format) {              
                case 'lastFirst':
                    if (salutation) value += salutation;
                    if (last) value += ' ' + last;
                    if (first) value += ' ' + first;
                    break;

                case 'lastFirstMiddle':
                    arr = [];
                    if (salutation) arr.push(salutation);
                    if (last) arr.push(last);
                    if (first) arr.push(first);
                    if (middle) arr.push(middle);
                    value = arr.join(' ');
                    break;

                case 'firstMiddleLast':
                    arr = [];
                    if (salutation) arr.push(salutation);
                    if (first) arr.push(first);
                    if (middle) arr.push(middle);
                    if (last) arr.push(last);
                    value = arr.join(' ');
                    break;

                case 'firstMiddleLastSuffix':
                    arr = [];
                    if (salutation) arr.push(salutation);
                    if (first) arr.push(first);
                    if (middle) arr.push(middle);
                    if (last) arr.push(last);
                    if (suffix) arr.push(suffix);
                    value = arr.join(' ');
                    break;

                case 'firstMiddleLastMother':
                    arr = [];
                    if (salutation) arr.push(salutation);
                    if (first) arr.push(first);
                    if (middle) arr.push(middle);
                    if (last) arr.push(last);
                    if (mother) arr.push(mother);
                    value = arr.join(' ');
                    break;

                case 'firstMiddleLastMotherSuffix':
                    arr = [];
                    if (salutation) arr.push(salutation);
                    if (first) arr.push(first);
                    if (middle) arr.push(middle);
                    if (last) arr.push(last);
                    if (mother) arr.push(mother);
                    if (suffix) arr.push(suffix);
                    value = arr.join(' ');
                    break;

                default:
                    if (salutation) value += salutation;
                    if (first) value += ' ' + first;
                    if (last) value += ' ' + last;
            }

            value = value.trim();

            return value;
        },

        _getTemplateName: function () {
            if (this.mode == 'edit') {
                var prop = 'editTemplate' + Espo.Utils.upperCaseFirst(this.getFormat().toString());
                if (prop in this) {
                    return this[prop];
                }
            }
            return Dep.prototype._getTemplateName.call(this);
        },

        getFormat: function () {
            this.format = this.format || this.getConfig().get('personNameFormat') || 'firstLast';

            return this.format;
        },

        formatHasMiddle: function () {
            var format = this.getFormat();

            return format === 'firstMiddleLast' || format === 'lastFirstMiddle' || format === 'firstMiddleLastSuffix' || format === 'firstMiddleLastMotherSuffix';
        },

        formatHasSuffix: function () {
            var format = this.getFormat();

            return format === 'firstMiddleLastSuffix' || format === 'firstMiddleLastMotherSuffix';
        },

        formatHasMother: function () {
            var format = this.getFormat();

            return format === 'firstMiddleLastMotherSuffix' || format === 'firstMiddleLastMother';
        },

        validateRequired: function () {
            var isRequired = this.isRequired();

            var validate = function (name) {
                if (this.model.isRequired(name)) {
                    if (!this.model.get(name)) {
                        var msg = this.translate('fieldIsRequired', 'messages').replace('{field}', this.translate(name, 'fields', this.model.name));
                        this.showValidationMessage(msg, '[data-name="'+name+'"]');
                        return true;
                    }
                }
            }.bind(this);

            if (isRequired) {
                if (!this.model.get(this.firstField) && !this.model.get(this.lastField)) {
                    var msg = this.translate('fieldIsRequired', 'messages').replace('{field}', this.getLabelText());
                    this.showValidationMessage(msg, '[data-name="'+this.lastField+'"]');
                    return true;
                }
            }

            var result = false;
            result = validate(this.salutationField) || result;
            result = validate(this.firstField) || result;
            result = validate(this.lastField) || result;
            result = validate(this.middleField) || result;
            result = validate(this.motherField) || result;
            result = validate(this.suffixField) || result;
            return result;
        },

        hasRequiredMarker: function () {
            if (this.isRequired()) return true;
            return this.model.getFieldParam(this.salutationField, 'required') ||
                   this.model.getFieldParam(this.firstField, 'required') ||
                   this.model.getFieldParam(this.middleField, 'required') ||
                   this.model.getFieldParam(this.lastField, 'required') || 
                   this.model.getFieldParam(this.motherField, 'required') || 
                   this.model.getFieldParam(this.suffixField, 'required');
        },

        fetch: function (form) {
            var data = {};
            data[this.salutationField] = this.$salutation.val() || null;
            data[this.firstField] = this.$first.val().trim() || null;
            data[this.lastField] = this.$last.val().trim() || null;
            data[this.motherField] = this.$mother.val().trim() || null;
            data[this.suffixField] = this.$suffix.val() || null;

            if (this.formatHasMiddle()) {
                data[this.middleField] = this.$middle.val().trim() || null;
            }

            if (this.formatHasMother()) {
                data[this.motherField] = this.$mother.val().trim() || null;
            }

            return data;
        }
    });
});
