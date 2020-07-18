import React, {useState} from 'react';
import {View, Text, ScrollView, TextInput, Button} from "react-native";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";
import {useDispatch, useSelector} from "react-redux";
import {login} from "../redux/actions";

const LoginScreen = function({navigation, route}) {
  const dispatch = useDispatch();
  const [username, onChangeUsernameText] = useState('');
  const [password, onChangePasswordText] = useState('');

  const loginErrors = useSelector(state => state.auth.errors);

  const submit = function() {
    dispatch(login(username, password)).then(() => navigation.navigate('Home'));
  }

  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.screen} contentInsetAdjustmentBehavior="automatic">
        <Text style={[Style.h1, {marginBottom: 20}]}>Login</Text>
        {
          loginErrors.length > 0 && (
            <View style={Style.formSummaryContainer}>
              <Text style={[Style.formError, Style.formSummaryLine]}>Couldn't log you in because:</Text>
              <View>{
                loginErrors.map(error => (
                  <Text key={error} style={Style.formError}>{error}</Text>
                ))
              }</View>
            </View>
          )
        }
        <View style={Style.formGroup}>
          <View style={Style.label}><Text>Username:</Text></View>
          <TextInput
            style={Style.textInput}
            onChangeText={text => onChangeUsernameText(text)}
            value={username}
          />
        </View>
        <View style={Style.formGroup}>
          <View style={Style.label}><Text>Password:</Text></View>
          <TextInput
            style={Style.textInput}
            onChangeText={text => onChangePasswordText(text)}
            value={password}
          />
        </View>
        <Button title="Login" onPress={() => submit()} />
      </ScrollView>
    </SafeAreaView>
  );
}

export default LoginScreen;
