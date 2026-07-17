(function (window, wp) {
	'use strict';
	if (!wp || !wp.element || !wp.components || !wp.blockEditor) return;

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var c = wp.components;
	var be = wp.blockEditor;
	var SSR = wp.serverSideRender;

	function dynamicEdit(blockName, props, controls) {
		return el(Fragment, {},
			el(be.InspectorControls, {}, el(c.PanelBody, { title: 'Настройки блока', initialOpen: true }, controls)),
			el('div', be.useBlockProps({ className: 'ap-editor-preview' }), el(SSR, { block: blockName, attributes: props.attributes }))
		);
	}

	function linesControl(label, value, onChange, help) {
		return el(c.TextareaControl, {
			label: label,
			value: Array.isArray(value) ? value.join('\n') : '',
			help: help || 'Один элемент на строку.',
			onChange: function (next) {
				onChange(next.split('\n').map(function (item) { return item.trim(); }).filter(Boolean));
			}
		});
	}

	function repeater(items, fields, onChange, addLabel) {
		items = Array.isArray(items) ? items : [];
		var rows = items.map(function (item, index) {
			var controls = fields.map(function (field) {
				var Component = field.type === 'textarea' ? c.TextareaControl : c.TextControl;
				return el(Component, {
					key: field.key,
					label: field.label,
					value: item[field.key] || '',
					onChange: function (value) {
						var next = items.slice();
						next[index] = Object.assign({}, next[index], (function () { var patch = {}; patch[field.key] = value; return patch; })());
						onChange(next);
					}
				});
			});
			controls.push(el(c.Button, {
				key: 'remove',
				isDestructive: true,
				isSmall: true,
				onClick: function () { var next = items.slice(); next.splice(index, 1); onChange(next); }
			}, 'Удалить'));
			return el('div', { key: index, className: 'ap-editor-repeater__row' }, controls);
		});
		rows.push(el(c.Button, {
			key: 'add',
			variant: 'secondary',
			onClick: function () {
				var fresh = {};
				fields.forEach(function (field) { fresh[field.key] = ''; });
				onChange(items.concat([fresh]));
			}
		}, addLabel || 'Добавить элемент'));
		return el('div', { className: 'ap-editor-repeater' }, rows);
	}

	window.AIPilotBlockEditor = {
		el: el,
		Fragment: Fragment,
		components: c,
		blockEditor: be,
		dynamicEdit: dynamicEdit,
		linesControl: linesControl,
		repeater: repeater
	};
})(window, window.wp);
