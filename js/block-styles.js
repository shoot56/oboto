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
});