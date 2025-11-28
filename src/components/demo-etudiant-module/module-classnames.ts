import { ModuleClassnamesParams, textOptionsClassnames } from '@divi/module';
import { DemoEtudiantModuleAttrs } from './types';


/**
 * Module classnames function for Demo Etudiant Module.
 *
 * @since ??
 *
 * @param {ModuleClassnamesParams<DemoEtudiantModuleAttrs>} param0 Function parameters.
 */
export const moduleClassnames = ({
  classnamesInstance,
  attrs,
}: ModuleClassnamesParams<DemoEtudiantModuleAttrs>): void => {
  // Text Options.
  classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text));
};
