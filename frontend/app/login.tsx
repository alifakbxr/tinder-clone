import React from 'react';
import { RecoilRoot } from 'recoil';
import LoginScreen from '../src/screens/LoginScreen';

export default function Login() {
  return (
    <RecoilRoot>
      <LoginScreen />
    </RecoilRoot>
  );
}