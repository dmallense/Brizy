import Config from "visual/global/Config";
import UIState from "visual/global/UIState";
import { AddElements } from "./components/AddElements";
import { BlocksSortable } from "./components/BlocksSortable";
import { Styling } from "./components/Styling";
import { Settings } from "./components/Settings";
import { DeviceModes } from "./components/DeviceModes";
import { t } from "visual/utils/i18n";

const urls = Config.get("urls");

export default {
  top: [AddElements, BlocksSortable, Styling],
  bottom: [
    DeviceModes,
    Settings,
    {
      id: "popover",
      icon: "nc-menu",
      title: t("More"),
      type: "popover",
      options: [
        {
          type: "link",
          icon: "nc-info",
          label: t("About Brizy"),
          link: urls.about,
          linkTarget: "_blank"
        },
        {
          type: "link",
          icon: "nc-alert-circle-que",
          label: t("Shortcuts"),
          link: "#",
          onClick: e => {
            e.preventDefault();

            UIState.set("prompt", {
              prompt: "key-helper"
            });
          }
        },
        {
          type: "link",
          icon: "nc-back",
          label: t("Go to Dashboard"),
          link: urls.backToDashboard
        }
      ]
    }
  ]
};
