# This is a simple script that compiles the plugin using MXMLC (free & cross-platform).
# To use, make sure you have downloaded and installed the Flex SDK in the following directory:
FLEXPATH=/Developer/SDKs/flex_sdk_3


echo "Compiling with MXMLC..."
$FLEXPATH/bin/mxmlc ./com/jeroenwijering/plugins/Audiodescription.as -sp ./ -o ./audiodescription.swf -use-network=false -target-player="10.0.0"