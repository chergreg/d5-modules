// Divi dependencies.
import { ModuleEditProps } from '@divi/module-library';
import {
  FormatBreakpointStateAttr,
  InternalAttrs,
  type Element,
  type Module,
} from '@divi/types';

export interface Image {
  src?: string;
  alt?: string;
}

export interface DemoEtudiantModuleCssAttr extends Module.Css.AttributeValue {
  contentContainer?: string;
  title?: string;
  description?: string;
  image?: string;
}

export type DemoEtudiantModuleCssGroupAttr = FormatBreakpointStateAttr<DemoEtudiantModuleCssAttr>;

export interface DemoEtudiantModuleAttrs extends InternalAttrs {
  // CSS options is used across multiple elements inside the module thus it deserves its own top property.
  css?: DemoEtudiantModuleCssGroupAttr;

  // Module
  module?: {
    meta?: Element.Meta.Attributes;
    advanced?: {
      link?: Element.Advanced.Link.Attributes;
      htmlAttributes?: Element.Advanced.IdClasses.Attributes;
      text?: Element.Advanced.Text.Attributes;
    };
    decoration?: Element.Decoration.PickedAttributes<
      'animation' |
      'background' |
      'border' |
      'boxShadow' |
      'disabledOn' |
      'filters' |
      'overflow' |
      'position' |
      'scroll' |
      'sizing' |
      'spacing' |
      'sticky' |
      'transform' |
      'transition' |
      'zIndex'
    >;
  };

  image?: {
    innerContent?: Element.Types.Image.InnerContent.Attributes;
    decoration?: Element.Decoration.PickedAttributes<
    'border' |
    'boxShadow' |
    'filters' |
    'spacing'
    >;
  };

  // Title
  title?: Element.Types.Title.Attributes;

  // Description
  description?: Element.Types.Content.Attributes;

}

export type DemoEtudiantModuleEditProps = ModuleEditProps<DemoEtudiantModuleAttrs>;
