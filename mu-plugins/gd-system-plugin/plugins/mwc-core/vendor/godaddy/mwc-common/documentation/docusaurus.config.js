const remarkAdmonitions = require('remark-admonitions');

module.exports = {
  title: 'Managed WooCommerce Common',
  tagline: 'The core to build them all',
  url: 'https://upgraded-lamp-d562c70b.pages.github.io', // Your website URL
  baseUrl: '/',
  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',
  favicon: 'img/favicon.ico',
  organizationName: 'gdcorp-partners', // Usually your GitHub org/user name.
  projectName: 'gdcorp-partners.github.io', // Usually your repo name.
  themeConfig: {
    hideableSidebar: true,
    navbar: {
      title: 'Managed WooCommerce Common',
      logo: {
        alt: 'My Site Logo',
        src: 'img/logo.svg',
      },
      items: [
        {
          href: 'https://github.com/gdcorp-partners/mwc-common',
          label: 'GitHub',
          position: 'right',
        },
      ],
    },
    footer: {
      copyright: `Copyright © ${new Date().getFullYear()}`,
    },
  },
  presets: [
    [
      '@docusaurus/preset-classic',
      {
        docs: {
          // Please change this to your repo.
          editUrl:
              'https://github.com/gdcorp-partners/mwc-common/tree/master/documentation',
          remarkPlugins: [remarkAdmonitions],
          routeBasePath: '/',
          sidebarPath: require.resolve('./sidebars.js'),
        },
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      },
    ],
  ],
};
