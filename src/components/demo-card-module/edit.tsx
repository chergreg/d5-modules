// External Dependencies.
import React, { ReactElement } from 'react';

// Divi Dependencies.
import { ModuleContainer } from '@divi/module';

// Local Dependencies.
import { DemoCardModuleEditProps } from './types';
import { ModuleStyles } from './styles';
import { moduleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';

import staticMetadata from './module.json';

/**
 * Demo Card Module edit component of visual builder.
 *
 * @since ??
 *
 * @param {DemoCardModuleEditProps} props React component props.
 *
 * @returns {ReactElement}
 */
export const DemoCardModuleEdit = (props: DemoCardModuleEditProps): ReactElement => {
  const {
    attrs,
    elements,
    id,
    name,
  } = props;

  // Variables de contenu (équivalentes à la version PHP).
  const videoId = 'HW2p_MxvmQI';
  const videoUrl = 'https://www.youtube.com/watch?v=HW2p_MxvmQI';
  const channelUrl = 'https://www.youtube.com/@benjamincode';
  const thumbUrl = 'https://i.ytimg.com/vi/HW2p_MxvmQI/maxresdefault.jpg';
  const thumbAlt = 'Miniature de la vidéo';
  const videoDuration = '22:15';

  const descriptionTitle = 'Description de la vidéo';
  const descriptionP1 =
    'Une liste de 125 idées de projets pour monter en compétence en développement&nbsp;: front, back, mobile, outils perso… De quoi t’entraîner sans jamais être à court d’inspiration.';
  const descriptionP2Prefix =
    'Tu peux retrouver la liste complète des idées sur la page dédiée :';
  const descriptionLinkUrl = 'https://benjamincode.com';
  const descriptionLinkLabel = 'voir les projets proposés';
  const descriptionP3 =
    'N’hésite pas à piocher plusieurs idées, les combiner, et en faire des projets concrets à ajouter à ton portfolio. Plus tu pratiques, plus tu progresses.';

  const avatarInitial = 'B';
  const ariaLabelChannel = 'Aller sur la chaîne YouTube de Benjamin Code';
  const videoTitle =
    "125 projets de DEV pour progresser (et ne plus jamais manquer d'idées)";
  const channelWithSubs = 'Benjamin Code (150k abonnés)';
  const metaLine = '3,3&nbsp;k&nbsp;vues • 1&nbsp;254&nbsp;likes • il y a 3&nbsp;heures';

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
      <div className="example_demo_card_module__inner">
        <div
          className="card-yt"
          data-video-id={videoId}
          data-video-url={videoUrl}
          data-channel-url={channelUrl}
        >
          {/* ZONE MÉDIA : flip + vidéo */}
          <div className="card-yt-media">
            <div className="card-yt-inner">
              {/* PILE : juste vignette, bouton et durée */}
              <div className="card-yt-face card-yt-front">
                <div className="card-yt-thumb">
                  <img src={thumbUrl} alt={thumbAlt} />
                  <div className="card-yt-playbtn" />
                  <div className="card-yt-duration">{videoDuration}</div>
                </div>
              </div>

              {/* FACE : description sur blur */}
              <div className="card-yt-face card-yt-back">
                <div className="card-yt-back-inner">
                  <div className="card-yt-back-gradient" />

                  {/* Petit play en haut à droite */}
                  <div className="card-yt-back-play" />

                  <div className="card-yt-back-content">
                    <h3>{descriptionTitle}</h3>
                    <p
                      dangerouslySetInnerHTML={{ __html: descriptionP1 }}
                    />
                    <p>
                      {descriptionP2Prefix}{' '}
                      <a
                        href={descriptionLinkUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        {descriptionLinkLabel}
                      </a>
                      .
                    </p>
                    <p>{descriptionP3}</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Vidéo insérée au clic */}
            <div className="card-yt-video-wrapper" />
          </div>

          {/* INFOS SOUS LA VIGNETTE */}
          <div className="card-yt-meta">
            <a
              className="card-yt-avatar-link"
              href={channelUrl}
              target="_blank"
              rel="noopener noreferrer"
              aria-label={ariaLabelChannel}
            >
              <div className="card-yt-avatar">{avatarInitial}</div>
            </a>

            <div className="card-yt-text">
              <a
                className="card-yt-title-link"
                href={videoUrl}
                target="_blank"
                rel="noopener noreferrer"
              >
                <h3 className="card-yt-title-line">{videoTitle}</h3>
              </a>

              <div className="card-yt-channel-row">
                <a
                  className="card-yt-channel-link"
                  href={channelUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {channelWithSubs}
                </a>
              </div>

              <div
                className="card-yt-meta-line"
                dangerouslySetInnerHTML={{ __html: metaLine }}
              />
            </div>
          </div>
        </div>
      </div>
    </ModuleContainer>
  );
};

