wp.domReady(() => {
  wp.blocks.registerBlockStyle("core/group", {
    name: "has-background-image",
    label: "Add background Image",
    isDefault: false,
  });
   wp.blocks.registerBlockStyle("core/group", {
    name: "has-overlay",
    label: "Add Overlay",
    isDefault: false,
  });
  wp.blocks.registerBlockStyle('core/list', {
    name: 'list-numbers',
    label: 'Full Numbers',
    isDefault: false,
  });
  wp.blocks.registerBlockStyle('core/list', {
    name: 'list-as-p',
    label: 'List plain text',
    isDefault: false,
  });
});