import { defineConfig } from 'tsup';

export default defineConfig({
  entry: {
    index: 'src/index.ts',
    'nextjs/index': 'src/nextjs/index.ts',
  },
  format: ['cjs', 'esm'],
  dts: true,
  splitting: false,
  sourcemap: true,
  clean: true,
  external: [
    'react',
    'react-dom',
    '@pdf-embed-seo/core',
    '@pdf-embed-seo/react',
    '@pdf-embed-seo/react-premium',
  ],
});
