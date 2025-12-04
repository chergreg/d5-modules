// External dependencies.
import React, {ReactElement} from 'react';

// Divi dependencies.
import {
  StyleContainer,
  StylesProps,
  CssStyle,
  TextStyle,
} from '@divi/module';

// Local dependencies.
import {DemoCardYTModuleAttrs} from './types';
import {cssFields} from './custom-css';

/**
 * Demo Card Y T Module's style components.
 *
 * @since ??
 */
export const ModuleStyles = ({
    attrs,
    elements,
    settings,
    orderClass,
    mode,
    state,
    noStyleTag,
  }: StylesProps<DemoCardYTModuleAttrs>): ReactElement => {
  const textSelector = `${orderClass} .example_demo_card_y_t_module__content-container`;

  return (
    <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
      {/* Module */}
      {elements.style({
        attrName: 'module',
        styleProps: {
          disabledOn: {
            disabledModuleVisibility: settings?.disabledModuleVisibility,
          },
          advancedStyles: [
            {
              componentName: "divi/text",
              props: {
                selector:textSelector ,
                attr: attrs?.module?.advanced?.text,
              }
            }
          ]
        },
      })}
      {/* Image */}
      {elements.style({
        attrName: 'image',
      })}

      {/* Title */}
      {elements.style({
        attrName: 'title',
      })}

      {/* Description */}
      {elements.style({
        attrName: 'description',
      })}


      {/*
       * We need to add CssStyle at the very bottom of other components
       * so that custom css can override module styles till we find a
       * more elegant solution.
       */}
      <CssStyle
        selector={orderClass}
        attr={attrs.css}
        cssFields={cssFields}
      />

    </StyleContainer>
  );
};
