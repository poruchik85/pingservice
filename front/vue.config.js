process.env.API_URI = "http://pingservice.local:8080/";

module.exports = {
  devServer: {
    disableHostCheck: true,
    port: 8080
  },
};
