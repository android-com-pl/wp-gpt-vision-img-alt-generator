import { Button } from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";
import { useState } from "@wordpress/element";
import { useDispatch } from "@wordpress/data";
import { store as noticesStore } from "@wordpress/notices";

import generateAltText from "../../utils/generateAltText";

export default ({
  attributes,
  setAttributes,
}: {
  attributes: ImageBlockAttrs;
  setAttributes: ImageBlockSetAttrs;
}) => {
  const [isGenerating, setIsGenerating] = useState(false);
  const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore);

  const handleClick = async () => {
    if (
      attributes.alt.length &&
      !confirm(
        __(
          "Are you sure you want to overwrite the existing alt text?",
          "acpl-ai-alt-generator",
        ),
      )
    ) {
      return;
    }

    try {
      setIsGenerating(true);

      const alt = await generateAltText(attributes.id);
      setAttributes({ alt });

      await createSuccessNotice(
        __("Alternative text generated", "acpl-ai-alt-generator"),
        {
          type: "snackbar",
          id: "alt-text-generated",
        },
      );
      //@ts-ignore
    } catch (error: WPError) {
      if (error.message) {
        await createErrorNotice(
          sprintf(
            __(
              "There was an error generating the alt text: %s",
              "acpl-ai-alt-generator",
            ),
            error.message,
          ),
          {
            id: "alt-text-error",
            type: "default",
          },
        );
      }
    } finally {
      setIsGenerating(false);
    }
  };

  return (
    <Button
      variant="primary"
      onClick={handleClick}
      isBusy={isGenerating}
      disabled={isGenerating}
    >
      {__("Generate alternative text", "acpl-ai-alt-generator")}
    </Button>
  );
};
