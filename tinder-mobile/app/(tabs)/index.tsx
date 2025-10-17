import React from 'react';
import { RecoilRoot } from 'recoil';
import MainScreen from '../../src/screens/MainScreen';

export default function HomeScreen() {
  return (
    <RecoilRoot>
      <MainScreen />
    </RecoilRoot>
  );
}
