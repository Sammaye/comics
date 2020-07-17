import React from 'react';
import {Text, ScrollView} from "react-native";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";

const AccountScreen = function () {
  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.page} contentInsetAdjustmentBehavior="automatic">
        <Text>Account Screen</Text>
      </ScrollView>
    </SafeAreaView>
  );
}

export default AccountScreen;
