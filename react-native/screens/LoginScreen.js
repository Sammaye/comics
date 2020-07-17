import React from 'react';
import {View, Text, ScrollView} from "react-native";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";

const LoginScreen = function() {
  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.page} contentInsetAdjustmentBehavior="automatic">
        <Text>Login Screen</Text>
      </ScrollView>
    </SafeAreaView>
  );
}

export default LoginScreen;
