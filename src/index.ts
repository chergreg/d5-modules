import { omit } from 'lodash';
import { addAction } from '@wordpress/hooks';
import { registerModule } from '@divi/module-library';
import { staticModule } from './components/static-module';
import { helloWorldModule } from './components/hello-world-module';
import { demoCardModule } from './components/demo-card-module';
import { demoCardYTModule } from './components/demo-card-y-t-module';
import { demoEtudiantModule } from './components/demo-etudiant-module';


import './module-icons';

// Register modules.
addAction(
  'divi.moduleLibrary.registerModuleLibraryStore.after',
  'extensionExample',
  () => {
    registerModule(staticModule.metadata, omit(staticModule, 'metadata') as any);
    registerModule(helloWorldModule.metadata, omit(helloWorldModule, 'metadata') as any);
    registerModule(demoCardModule.metadata, omit(demoCardModule, 'metadata') as any);
    registerModule(demoCardYTModule.metadata, omit(demoCardYTModule, 'metadata') as any);
    registerModule(demoEtudiantModule.metadata, omit(demoEtudiantModule, 'metadata') as any);
  }
);
