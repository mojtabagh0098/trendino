(() => {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const {
		RichText,
		InspectorControls,
		MediaUpload,
		MediaUploadCheck,
		useBlockProps,
	} = wp.blockEditor;
	const {
		PanelBody,
		SelectControl,
		TextControl,
		ToggleControl,
		RangeControl,
		Button,
		ColorPalette,
	} = wp.components;

	const HIGHLIGHT_PRESETS = {
		rose: { bg: '#FDF0F2', dot: '#E81327', title: '#1F2937', text: '#4B5563' },
		blue: { bg: '#EFF6FF', dot: '#2563EB', title: '#1E3A8A', text: '#374151' },
		green: { bg: '#EBF9F3', dot: '#10B981', title: '#065F46', text: '#374151' },
		amber: { bg: '#FFFBEB', dot: '#D97706', title: '#78350F', text: '#4B5563' },
	};

	/* ======================================================================
	   1. Highlight / Note Box Block (باکس نکته و تحلیل ترندینو)
	   ====================================================================== */
	registerBlockType('trendino/highlight-box', {
		title: 'باکس نکته و تحلیل ترندینو',
		description: 'باکس رنگی همراه با نقطه رنگی، تیتر و متن توضیحی برای مقالات بلاگ.',
		category: 'trendino',
		icon: 'megaphone',
		keywords: ['نکته', 'باکس', 'تحلیل', 'ترندینو', 'highlight', 'callout'],
		attributes: {
			title: {
				type: 'string',
				source: 'html',
				selector: '.tno-gb-highlight__title-text',
				default: 'جهش‌های هیجانی در بازار معاملات آنی',
			},
			content: {
				type: 'string',
				source: 'html',
				selector: '.tno-gb-highlight__text',
				default: 'بررسی‌ها نشان می‌دهد بخش عمده‌ای از نوسانات اخیر ناشی از تقاضای لحظه‌ای و رفتارهای هیجانی بازار بوده است؛ بنابراین تصمیم‌گیری منطقی و بررسی داده‌های واقعی پیش از خرید اهمیت بسیار بالایی دارد.',
			},
			tone: {
				type: 'string',
				default: 'rose',
			},
			customBg: {
				type: 'string',
				default: '',
			},
			customDot: {
				type: 'string',
				default: '',
			},
		},

		edit({ attributes, setAttributes }) {
			const { title, content, tone, customBg, customDot } = attributes;
			const preset = HIGHLIGHT_PRESETS[tone] || HIGHLIGHT_PRESETS.rose;
			const bgColor = customBg || preset.bg;
			const dotColor = customDot || preset.dot;

			const blockProps = useBlockProps({
				className: 'tno-gb-highlight',
				dir: 'rtl',
				style: { backgroundColor: bgColor },
			});

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'تنظیمات رنگ باکس', initialOpen: true },
						el(SelectControl, {
							label: 'قالب رنگی آماده',
							value: tone,
							options: [
								{ label: 'صورتی / قرمز ملایم (پیش‌فرض)', value: 'rose' },
								{ label: 'آبی ملایم', value: 'blue' },
								{ label: 'سبز ملایم', value: 'green' },
								{ label: 'زرد / کهربایی ملایم', value: 'amber' },
							],
							onChange: (val) => setAttributes({ tone: val, customBg: '', customDot: '' }),
						}),
						el('p', { style: { fontSize: '12px', marginBottom: '6px' } }, 'رنگ پس‌زمینه دلخواه'),
						el(ColorPalette, {
							value: bgColor,
							onChange: (val) => setAttributes({ customBg: val || '' }),
						}),
						el('p', { style: { fontSize: '12px', marginBottom: '6px' } }, 'رنگ نقطه کنار عنوان'),
						el(ColorPalette, {
							value: dotColor,
							onChange: (val) => setAttributes({ customDot: val || '' }),
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'tno-gb-highlight__head' },
						el('span', {
							className: 'tno-gb-highlight__dot',
							style: { backgroundColor: dotColor },
							'aria-hidden': 'true',
						}),
						el(RichText, {
							tagName: 'div',
							className: 'tno-gb-highlight__title-text',
							value: title,
							onChange: (val) => setAttributes({ title: val }),
							placeholder: 'عنوان باکس را بنویسید...',
						})
					),
					el(RichText, {
						tagName: 'p',
						className: 'tno-gb-highlight__text',
						value: content,
						onChange: (val) => setAttributes({ content: val }),
						placeholder: 'متن داخل باکس را وارد کنید...',
					})
				)
			);
		},

		save({ attributes }) {
			const { title, content, tone, customBg, customDot } = attributes;
			const preset = HIGHLIGHT_PRESETS[tone] || HIGHLIGHT_PRESETS.rose;
			const bgColor = customBg || preset.bg;
			const dotColor = customDot || preset.dot;

			const blockProps = useBlockProps.save({
				className: 'tno-gb-highlight',
				dir: 'rtl',
				style: { backgroundColor: bgColor },
			});

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'tno-gb-highlight__head' },
					el('span', {
						className: 'tno-gb-highlight__dot',
						style: { backgroundColor: dotColor },
						'aria-hidden': 'true',
					}),
					el(RichText.Content, {
						tagName: 'div',
						className: 'tno-gb-highlight__title-text',
						value: title,
					})
				),
				el(RichText.Content, {
					tagName: 'p',
					className: 'tno-gb-highlight__text',
					value: content,
				})
			);
		},
	});

	/* ======================================================================
	   2. Ad Banner Block (باکس محل تبلیغات ترندینو)
	   ====================================================================== */
	registerBlockType('trendino/ad-banner', {
		title: 'باکس تبلیغات ترندینو',
		description: 'باکس اختصاصی بنر تبلیغاتی در میانه مقالات (حالت متنی یا تصویری همراه با لینک).',
		category: 'trendino',
		icon: 'format-image',
		keywords: ['تبلیغات', 'بنر', 'آگهی', 'ترندینو', 'ad', 'banner'],
		attributes: {
			mode: {
				type: 'string',
				default: 'text',
			},
			label: {
				type: 'string',
				source: 'html',
				selector: '.tno-gb-ad__label',
				default: 'محل تبلیغات شما',
			},
			imageUrl: {
				type: 'string',
				default: '',
			},
			imageAlt: {
				type: 'string',
				default: '',
			},
			linkUrl: {
				type: 'string',
				default: '',
			},
			openNewTab: {
				type: 'boolean',
				default: true,
			},
			bgColor: {
				type: 'string',
				default: '#E8F1FC',
			},
			textColor: {
				type: 'string',
				default: '#5C8FD6',
			},
			minHeight: {
				type: 'number',
				default: 118,
			},
		},

		edit({ attributes, setAttributes }) {
			const { mode, label, imageUrl, imageAlt, linkUrl, openNewTab, bgColor, textColor, minHeight } = attributes;

			const blockProps = useBlockProps({
				className: 'tno-gb-ad' + (mode === 'image' && imageUrl ? ' is-image' : ''),
				dir: 'rtl',
				style: {
					backgroundColor: bgColor,
					minHeight: mode === 'image' && imageUrl ? 'auto' : `${minHeight}px`,
				},
			});

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'تنظیمات بنر تبلیغاتی', initialOpen: true },
						el(SelectControl, {
							label: 'نوع نمایش تبلیغ',
							value: mode,
							options: [
								{ label: 'باکس متنی (محل تبلیغات شما)', value: 'text' },
								{ label: 'تصویر بنر تبلیغاتی', value: 'image' },
							],
							onChange: (val) => setAttributes({ mode: val }),
						}),
						mode === 'image' &&
							el(
								MediaUploadCheck,
								null,
								el(MediaUpload, {
									onSelect: (media) =>
										setAttributes({
											imageUrl: media.url || '',
											imageAlt: media.alt || '',
										}),
									allowedTypes: ['image'],
									render: ({ open }) =>
										el(
											'div',
											{ style: { marginBottom: '14px' } },
											el(
												Button,
												{ variant: 'secondary', onClick: open },
												imageUrl ? 'تغییر تصویر بنر' : 'انتخاب تصویر بنر'
											),
											imageUrl &&
												el(
													Button,
													{
														variant: 'link',
														isDestructive: true,
														onClick: () => setAttributes({ imageUrl: '', imageAlt: '' }),
														style: { marginRight: '8px' },
													},
													'حذف'
												)
										),
								})
							),
						el(TextControl, {
							label: 'لینک کلیک بنر (اختیاری)',
							value: linkUrl,
							placeholder: 'https://...',
							onChange: (val) => setAttributes({ linkUrl: val }),
						}),
						el(ToggleControl, {
							label: 'باز شدن لینک در تب جدید',
							checked: openNewTab,
							onChange: (val) => setAttributes({ openNewTab: val }),
						}),
						el(RangeControl, {
							label: 'حداقل ارتفاع باکس (پیکسل)',
							value: minHeight,
							min: 70,
							max: 320,
							onChange: (val) => setAttributes({ minHeight: val }),
						}),
						el('p', { style: { fontSize: '12px', marginBottom: '6px' } }, 'رنگ پس‌زمینه'),
						el(ColorPalette, {
							value: bgColor,
							onChange: (val) => setAttributes({ bgColor: val || '#E8F1FC' }),
						}),
						mode === 'text' && el('p', { style: { fontSize: '12px', marginBottom: '6px' } }, 'رنگ متن'),
						mode === 'text' &&
							el(ColorPalette, {
								value: textColor,
								onChange: (val) => setAttributes({ textColor: val || '#5C8FD6' }),
							})
					)
				),
				el(
					'div',
					blockProps,
					mode === 'image' && imageUrl
						? el('img', {
								src: imageUrl,
								alt: imageAlt || 'بنر تبلیغاتی',
								className: 'tno-gb-ad__img',
						  })
						: el(RichText, {
								tagName: 'div',
								className: 'tno-gb-ad__label',
								value: label,
								onChange: (val) => setAttributes({ label: val }),
								placeholder: 'محل تبلیغات شما',
								style: { color: textColor },
						  })
				)
			);
		},

		save({ attributes }) {
			const { mode, label, imageUrl, imageAlt, linkUrl, openNewTab, bgColor, textColor, minHeight } = attributes;

			const blockProps = useBlockProps.save({
				className: 'tno-gb-ad' + (mode === 'image' && imageUrl ? ' is-image' : ''),
				dir: 'rtl',
				style: {
					backgroundColor: bgColor,
					minHeight: mode === 'image' && imageUrl ? 'auto' : `${minHeight}px`,
				},
			});

			const inner =
				mode === 'image' && imageUrl
					? el('img', {
							src: imageUrl,
							alt: imageAlt || 'بنر تبلیغاتی',
							className: 'tno-gb-ad__img',
							loading: 'lazy',
					  })
					: el(RichText.Content, {
							tagName: 'div',
							className: 'tno-gb-ad__label',
							value: label,
							style: { color: textColor },
					  });

			if (linkUrl) {
				return el(
					'div',
					blockProps,
					el(
						'a',
						{
							href: linkUrl,
							className: 'tno-gb-ad__link',
							target: openNewTab ? '_blank' : undefined,
							rel: openNewTab ? 'noopener noreferrer nofollow' : undefined,
						},
						inner
					)
				);
			}

			return el('div', blockProps, inner);
		},
	});
})();
