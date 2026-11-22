document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tno-media').forEach(container => {
    const input = container.querySelector('input'), img = container.querySelector('img');
    const preview = () => { img.src = input.value; img.hidden = !input.value; };
    container.querySelector('.tno-media-select').addEventListener('click', () => {
      if (!window.wp || !wp.media) return;
      const frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده از تصویر' }, library: { type: 'image' }, multiple: false });
      frame.on('select', () => { input.value = frame.state().get('selection').first().toJSON().url; preview(); }); frame.open();
    });
    container.querySelector('.tno-media-clear').addEventListener('click', () => { input.value = ''; preview(); }); input.addEventListener('change', preview);
  });
});
