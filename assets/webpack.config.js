//path モジュールの読み込み
const path = require('path');
//MiniCssExtractPlugin の読み込み
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
//変数 devMode は production モードの場合は false でその他の場合は true
const devMode = process.env.NODE_ENV !== 'production';

module.exports = {

  //エントリポイント（デフォルトと同じなので省略可）
    entry: './src/index.js',
  //出力先
    output: {
    filename: 'main.js',
    path: path.resolve(__dirname, 'dist'),
    //ファイルを出力する前にディレクトリをクリーンアップ
    clean: true,
    //Asset Modules の出力先の指定
    assetModuleFilename: 'images/[name][ext][query]'
    },
    module: {
    rules: [
      //SASS 及び CSS 用のローダー
        {
        //拡張子 .scss、.sass、css を対象
        test: /\.(scss|sass|css)$/i,
        // 使用するローダーの指定
        use: [
          //CSS を別ファイルとして出力するプラグインのローダー
            MiniCssExtractPlugin.loader,
            {
            loader: "css-loader",
            options: {
                importLoaders: 2
            },
            },
          // PostCSS ローダーの設定
            {
            loader: "postcss-loader",
            options: {
                postcssOptions: {
                plugins: [
                    [
                    "postcss-preset-env",
                    {
                      //必要に応じて postcss-preset-env のオプションを指定
                    },
                    ],
                ],
                },
            },
            },
          //Sass ローダー
            'sass-loader',
        ],
        },
      // Babel 用のローダー
        {
        // ローダーの処理対象ファイル（拡張子 .js のファイルを対象）
        test: /\.js$/,
        // ローダーの処理対象から外すディレクトリ
        exclude: /node_modules/,
        // 処理対象のファイルに使用するローダーやオプションを指定
        use: [
            {
            // 利用するローダーを指定
            loader: 'babel-loader',
            // ローダー（babel-loader）のオプションを指定
            options: {
              // プリセットを指定
                presets: [
                // targets を指定していないので、一律に ES5 の構文に変換
                '@babel/preset-env',
                ]
            }
            }
        ]
        },
      //Asset Modules の設定
        {
        //対象とするアセットファイルの拡張子を正規表現で指定
        test: /\.(png|svg|jpe?g|gif)$/i,
        //画像をコピーして出力
        type: 'asset/resource'
        },
    ],
    },
  //プラグインの設定
    plugins: [
    new MiniCssExtractPlugin({
      // 抽出する CSS のファイル名
        filename: 'style.css',
    }),
    ],
  //production モード以外の場合は source-map タイプのソースマップを出力
    devtool: devMode ? 'source-map' : 'eval',
  // node_modules を監視（watch）対象から除外
    watchOptions: {
    ignored: /node_modules/  //正規表現で指定
    },
    

    devServer: {  
      //表示する静的ファイルのディレクトリを指定
      static: {
        //対象のディレクトリを指定
        directory: path.join(__dirname, '../'),
      },
      // または static: '../',
      //サーバー起動時にブラウザを自動的に起動
      open: true,
      // ポート番号をデフォルトの 8080 から 3000 に変更
      port: 3000,
      //webpack-dev-middleware 関連の設定
      devMiddleware: {
        writeToDisk: true, //バンドルされたファイルを出力する（実際に書き出す）
      },
    },
};