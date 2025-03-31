const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    devtool: "source-map",
    entry: {
        guide_switch: "./src/guide-switch.ts",
        guide: "./src/guide.ts",
    },
    output: {
        filename: "[name].js",
        path: path.resolve(__dirname, "../Resources/Public/JavaScript"),
        library: {
            type: "module",
        },
    },
    experiments: {
        // Allows Webpack to output ES modules
        outputModule: true,
    },
    resolve: {
        extensions: [".ts", ".js"],
        alias: {
            "@macopedia/typo3-interactive-tour/tour": path.resolve(
                __dirname,
                "src/tour.ts"
            ),
            "@macopedia/typo3-interactive-tour/typo3/ajax-request":
                path.resolve(__dirname, "src/typo3/ajax-request.ts"),
            "@macopedia/typo3-interactive-tour/typo3/ajax-response":
                path.resolve(__dirname, "src/typo3/ajax-response.ts"),
            "@macopedia/typo3-interactive-tour/typo3/input-transformer":
                path.resolve(__dirname, "src/typo3/input-transformer.ts"),
            "@macopedia/typo3-interactive-tour/typo3/simple-response-interface":
                path.resolve(
                    __dirname,
                    "src/typo3/simple-response-interface.ts"
                ),
            "@macopedia/typo3-interactive-tour/typo3/document-service":
                path.resolve(__dirname, "src/typo3/document-service.ts"),
        },
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                use: "ts-loader",
                exclude: /node_modules/,
            },
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, "css-loader"],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "../StyleSheets/[name].css",
        }),
    ],
};
