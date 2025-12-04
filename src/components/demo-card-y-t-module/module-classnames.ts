import { ModuleClassnamesParams, textOptionsClassnames } from '@divi/module';
import { DemoCardYTModuleAttrs } from './types';


/**
 * Module classnames function for Demo Card Y T Module.
 *
 * @since ??
 *
 * @param {ModuleClassnamesParams<DemoCardYTModuleAttrs>} param0 Function parameters.
 */
export const moduleClassnames = ({
  classnamesInstance,
  attrs,
}: ModuleClassnamesParams<DemoCardYTModuleAttrs>): void => {
  // Text Options.
  classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text));
};
