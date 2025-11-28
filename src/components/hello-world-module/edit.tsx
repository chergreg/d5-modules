// External Dependencies.
import React, { ReactElement } from 'react';

// Divi Dependencies.
import { ModuleContainer } from '@divi/module';

// Local Dependencies.
import { HelloWorldModuleEditProps } from './types';
import { ModuleStyles } from './styles';
import { moduleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';

import staticMetadata from './module.json';

/**
 * Hello World Module edit component of visual builder.
 *
 * @since ??
 *
 * @param {HelloWorldModuleEditProps} props React component props.
 *
 * @returns {ReactElement}
 */
export const HelloWorldModuleEdit = (props: HelloWorldModuleEditProps): ReactElement => {
  const {
    attrs,
    elements,
    id,
    name,
  } = props;

  // Get image attributes.
  const imageSrc = attrs?.image?.innerContent?.desktop?.value?.src ?? ''
  const imageAlt = attrs?.image?.innerContent?.desktop?.value?.alt ?? '';

  // When summary is reset, placeholder is displayed since reset restores default value.
  // When summary is empty, placeholder is hidden since empty string indicates intentional user action.
  // To modify this behavior, developers can update the summary attribute with a default value during module render.
  // if ('' === attrs?.summary?.innerContent?.desktop?.value) {
  //   elements.attrs = {
  //     ...elements.attrs,
  //     summary: {
  //       ...elements.attrs.summary,
  //       innerContent: {
  //         desktop: {
  //           value: staticMetadata?.attributes?.summary?.default?.innerContent?.desktop?.value ?? '',
  //         },
  //       },
  //     },
  //   };
  // }

  return (
    <ModuleContainer
      attrs={attrs}
      elements={elements}
      id={id}
      name={name}
      stylesComponent={ModuleStyles}
      classnamesFunction={moduleClassnames}
      scriptDataComponent={ModuleScriptData}
    >
      {elements.styleComponents({
        attrName: 'module',
      })}
      <div className="example_hello_world_module__inner">        
        <img src={imageSrc} alt={imageAlt} />        
        {elements.render({
          attrName: 'title',
        })}
        {elements.render({
          attrName: 'description',
        })}
      </div>
    </ModuleContainer>
  );
};
