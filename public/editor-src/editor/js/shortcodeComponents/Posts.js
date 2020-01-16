const { t } = global.Brizy;

export default {
  id: "posts",
  title: t("Posts"),
  icon: "nc-wp-shortcode",
  resolve: {
    type: "Wrapper",
    value: {
      _styles: ["wrapper", "wrapper--posts", "wrapper--posts-dynamic"],
      items: [
        {
          type: "Posts",
          value: {
            _styles: ["posts-dynamic"],
            items: [
              {
                type: "Column",
                value: {
                  _styles: ["posts--column"],
                  items: [
                    {
                      type: "Wrapper",
                      value: {
                        _styles: ["wrapper", "wrapper--image"],
                        items: [
                          {
                            type: "Image",
                            value: {
                              _styles: ["image", "image--dynamic"]
                            }
                          }
                        ]
                      }
                    },
                    {
                      type: "Wrapper",
                      value: {
                        _styles: ["wrapper", "wrapper--richText"],
                        items: [
                          {
                            type: "RichText",
                            value: {
                              _styles: [
                                "richText",
                                "richText-title--dynamic",
                                "richText--dynamic"
                              ]
                            }
                          }
                        ]
                      }
                    },
                    {
                      type: "Wrapper",
                      value: {
                        _styles: ["wrapper", "wrapper--richText"],
                        items: [
                          {
                            type: "RichText",
                            value: {
                              _styles: [
                                "richText",
                                "richText-desc--dynamic",
                                "richText--dynamic"
                              ]
                            }
                          }
                        ]
                      }
                    },
                    {
                      type: "Cloneable",
                      value: {
                        _styles: ["wrapper-clone", "wrapper-clone--button"],
                        items: [
                          {
                            type: "Button",
                            value: {
                              _styles: ["button", "button--dynamic"]
                            }
                          }
                        ]
                      }
                    }
                  ]
                }
              }
            ]
          }
        }
      ]
    }
  }
};
