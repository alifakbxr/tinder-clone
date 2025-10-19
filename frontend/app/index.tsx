import React from 'react';
import { RecoilRoot } from 'recoil';

export default function AppEntry() {
  return (
    <RecoilRoot>
      {/* App content will be handled by Expo Router in _layout.tsx */}
    </RecoilRoot>
  );
}