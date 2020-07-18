import React, {useEffect} from 'react';
import {useDispatch, useSelector} from "react-redux";
import {ScrollView, Text} from 'react-native';
import {logout, requestLogout} from "../redux/actions";
import Style from "../Style";
import {SafeAreaView} from "react-native-safe-area-context";

const LogoutScreen = function({navigation, route}) {
  const dispatch = useDispatch();
  const token = useSelector(state => state.auth.token);

  useEffect(() => {
    const logoutEvent = navigation.addListener('focus', () => {
      dispatch(logout(token)).then(response => {
        navigation.navigate('Home');
      });
    });

    return logoutEvent;
  }, [navigation]);

  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.screen} contentInsetAdjustmentBehavior="automatic">
        <Text style={Style.h1}>Logging you out, mofo</Text>
      </ScrollView>
    </SafeAreaView>
  );
}

export default LogoutScreen;
