// Divi dependencies.
import {
  type Metadata,
  type ModuleLibrary,
} from '@divi/types';

// Local dependencies.
import metadata from './module.json';
// Ces deux imports ne sont plus utilisés ici.
// Tu peux les garder pour PHP ou ailleurs,
// mais ils ne servent plus dans cette définition TS.
//// import defaultRenderAttributes from './module-default-render-attributes.json';
//// import defaultPrintedStyleAttributes from './module-default-printed-style-attributes.json';
import { DemoEtudiantModuleEdit } from './edit';
import { DemoEtudiantModuleAttrs } from './types';
import { placeholderContent } from './placeholder-content';

// Styles.
import './style.scss';
import './module.scss';

export const demoEtudiantModule: ModuleLibrary.Module.RegisterDefinition<DemoEtudiantModuleAttrs> = {
  // Imported json has no inferred type hence type-cast is necessary.
  metadata: metadata as Metadata.Values<DemoEtudiantModuleAttrs>,

  // ❌ Ces propriétés ne sont plus supportées par RegisterDefinition
  // defaultAttrs:             defaultRenderAttributes as Metadata.DefaultAttributes<DemoEtudiantModuleAttrs>,
  // defaultPrintedStyleAttrs: defaultPrintedStyleAttributes as Metadata.DefaultAttributes<DemoEtudiantModuleAttrs>,

  placeholderContent,
  renderers: {
    edit: DemoEtudiantModuleEdit,
  },
};
