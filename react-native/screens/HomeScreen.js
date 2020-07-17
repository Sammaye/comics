import React from 'react';
import {ScrollView, Text, View, StyleSheet} from "react-native";
import {useSelector} from "react-redux";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";

const HomeScreen = function () {
  const state = useSelector(state => state);
  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.page} contentInsetAdjustmentBehavior="automatic">
        <Text>Home Screen</Text>
      </ScrollView>
    </SafeAreaView>
  );
}

export default HomeScreen;
