import React, {
  Fragment,
  ReactElement,
} from 'react';

import {
  ModuleScriptDataProps,
} from '@divi/module';
import { DemoEtudiantModuleAttrs } from './types';


/**
 * Static module's script data component.
 *
 * @since ??
 *
 * @param {ModuleScriptDataProps<DemoEtudiantModuleAttrs>} props React component props.
 *
 * @returns {ReactElement}
 */
export const ModuleScriptData = ({
  elements,
}: ModuleScriptDataProps<DemoEtudiantModuleAttrs>): ReactElement => (
  <Fragment>
    {elements.scriptData({
      attrName: 'module',
    })}
  </Fragment>
);

